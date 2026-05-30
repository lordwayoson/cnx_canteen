import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import dotenv from 'dotenv';
import { google } from 'googleapis';
import mysql from 'mysql2/promise';
import crypto from 'crypto';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
dotenv.config({ path: path.resolve(__dirname, '../.env') });

process.env.TZ = process.env.TZ || 'Africa/Accra';

const DEFAULT_COLUMNS = process.env.GOOGLE_SHEETS_COLUMNS || 'A1:U';
const dayKeys = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

function coerceDate(value) {
  if (value instanceof Date && !Number.isNaN(value.getTime())) {
    return new Date(value.getTime());
  }
  if (value === undefined || value === null) {
    return new Date();
  }
  const raw = String(value).trim();
  if (!raw) {
    return new Date();
  }

  const attemptParse = (candidate) => {
    const parsed = new Date(candidate);
    return Number.isNaN(parsed.getTime()) ? null : parsed;
  };

  let parsed = attemptParse(raw);
  if (parsed) {
    return parsed;
  }

  const isoLike = raw.replace(' ', 'T');
  parsed = attemptParse(isoLike);
  if (parsed) {
    return parsed;
  }

  const match = raw.match(/^([0-9]{1,2})[\/\-]([0-9]{1,2})[\/\-]([0-9]{2,4})(?:\s+([0-9]{1,2}):([0-9]{2})(?::([0-9]{2}))?)?$/);
  if (match) {
    let [_, part1, part2, part3, hour = '0', minute = '0', second = '0'] = match;
    let day = Number.parseInt(part1, 10);
    let month = Number.parseInt(part2, 10);
    let year = Number.parseInt(part3, 10);

    if (year < 100) {
      year += year >= 70 ? 1900 : 2000;
    }

    if (month > 12 && day <= 12) {
      const tmp = day;
      day = month;
      month = tmp;
    }

    if (day <= 12 && month <= 12 && day !== month) {
      // Ambiguous format. Prefer treating part1 as day if greater than 12 was already handled.
      // Nothing to change here; rely on the numbers as-is.
    }

    const isoString = `${year.toString().padStart(4, '0')}-${month.toString().padStart(2, '0')}-${day
      .toString()
      .padStart(2, '0')}T${hour.padStart(2, '0')}:${minute.padStart(2, '0')}:${second.padStart(2, '0')}`;
    parsed = attemptParse(isoString);
    if (parsed) {
      return parsed;
    }
  }

  const fallback = attemptParse(raw.replace(/[\/]/g, '-'));
  if (fallback) {
    return fallback;
  }

  return new Date();
}

function getWeekStartDate(timestamp) {
  const baseDate = coerceDate(timestamp);
  const monday = new Date(baseDate.getTime());
  monday.setHours(0, 0, 0, 0);
  const day = monday.getDay();
  const diff = monday.getDate() - day + (day === 0 ? -6 : 1);
  monday.setDate(diff);
  return monday.toISOString().slice(0, 10);
}

function parseServiceAccountJson(contents, source) {
  try {
    return JSON.parse(contents);
  } catch (error) {
    throw new Error(`Invalid Google service account JSON (${source}): ${error.message}`);
  }
}

function resolveCredentialCandidates(rawValue) {
  if (!rawValue) {
    return [];
  }
  const trimmed = rawValue.trim();
  if (!trimmed) {
    return [];
  }

  // Remove wrapping quotes that might be present in Windows .env files
  const unquoted = trimmed.replace(/^"|"$/g, '').replace(/^'|'$/g, '');
  const candidates = new Set();
  candidates.add(unquoted);
  if (path.isAbsolute(unquoted)) {
    candidates.add(path.normalize(unquoted));
  }
  candidates.add(path.resolve(process.cwd(), unquoted));
  candidates.add(path.resolve(__dirname, '..', unquoted));
  candidates.add(path.resolve(__dirname, unquoted));
  return Array.from(candidates);
}

function findDefaultCredentialPaths() {
  const repoRoot = path.resolve(__dirname, '..');
  const cwd = process.cwd();
  return [
    path.resolve(repoRoot, 'storage', 'service-account.json'),
    path.resolve(cwd, 'storage', 'service-account.json'),
    path.resolve(cwd, '..', 'storage', 'service-account.json')
  ];
}

function loadCredentialsFromPaths(paths) {
  for (const candidate of paths) {
    try {
      if (fs.existsSync(candidate)) {
        const contents = fs.readFileSync(candidate, 'utf8');
        return parseServiceAccountJson(contents, candidate);
      }
    } catch (error) {
      // Ignore filesystem errors so remaining candidates are still considered.
    }
  }
  return null;
}

function getServiceAccountCredentials() {
  const rawSetting = process.env.GOOGLE_SERVICE_JSON || '';
  const trimmed = rawSetting.trim();

  if (!trimmed) {
    const defaultCredentials = loadCredentialsFromPaths(findDefaultCredentialPaths());
    if (defaultCredentials) {
      return defaultCredentials;
    }
    throw new Error(
      'GOOGLE_SERVICE_JSON is not set. Provide a path, base64 payload, inline JSON, or place credentials at storage/service-account.json.'
    );
  }

  if (trimmed.startsWith('{')) {
    return parseServiceAccountJson(trimmed, 'inline .env value');
  }

  if (trimmed.toLowerCase().startsWith('base64:')) {
    const base64Payload = trimmed.slice(7);
    const decoded = Buffer.from(base64Payload, 'base64').toString('utf8');
    return parseServiceAccountJson(decoded, 'base64-encoded GOOGLE_SERVICE_JSON');
  }

  const candidates = resolveCredentialCandidates(trimmed);
  const credentials = loadCredentialsFromPaths(candidates);
  if (credentials) {
    return credentials;
  }

  throw new Error(
    `Google service account credentials not provided. Checked locations: ${candidates.join(', ') || '(none)'}`
  );
}

async function authorize() {
  const credentials = getServiceAccountCredentials();
  const scopes = ['https://www.googleapis.com/auth/spreadsheets.readonly'];
  const auth = new google.auth.GoogleAuth({
    credentials,
    scopes
  });
  return auth.getClient();
}

async function resolveSheetRange(sheetsClient, spreadsheetId) {
  const explicitRange = (process.env.GOOGLE_SHEETS_RANGE || '').trim();
  if (explicitRange) {
    return explicitRange;
  }

  const explicitTitle = (process.env.GOOGLE_SHEETS_TITLE || '').trim();
  if (explicitTitle) {
    return `${explicitTitle}!${DEFAULT_COLUMNS}`;
  }

  const targetGidRaw = (process.env.GOOGLE_SHEETS_GID || '').trim();
  const targetGid = targetGidRaw === '' ? null : Number.parseInt(targetGidRaw, 10);

  const { data } = await sheetsClient.spreadsheets.get({
    spreadsheetId,
    fields: 'sheets(properties(sheetId,title,hidden))'
  });

  const sheets = data.sheets || [];
  let chosenSheet = null;

  if (Number.isInteger(targetGid)) {
    chosenSheet = sheets.find((sheet) => sheet?.properties?.sheetId === targetGid) || null;
  }

  if (!chosenSheet) {
    chosenSheet = sheets.find((sheet) => sheet?.properties?.title === 'Form Responses 1') || null;
  }

  if (!chosenSheet) {
    chosenSheet = sheets.find((sheet) => sheet?.properties && sheet.properties.hidden !== true) || sheets[0] || null;
  }

  const title = chosenSheet?.properties?.title;
  if (!title) {
    throw new Error('Unable to determine Google Sheet tab title. Set GOOGLE_SHEETS_TITLE or GOOGLE_SHEETS_RANGE.');
  }

  return `${title}!${DEFAULT_COLUMNS}`;
}

export async function fetchRows() {
  const authClient = await authorize();
  const sheetsClient = google.sheets({ version: 'v4', auth: authClient });
  const spreadsheetId = process.env.GOOGLE_SHEETS_ID;
  if (!spreadsheetId) {
    throw new Error('GOOGLE_SHEETS_ID is not set');
  }
  const range = await resolveSheetRange(sheetsClient, spreadsheetId);
  const { data } = await sheetsClient.spreadsheets.values.get({ spreadsheetId, range });
  return data.values || [];
}

export function normalizeRows(values) {
  if (!values.length) {
    return [];
  }
  const [header, ...rows] = values;
  return rows
    .map((row, index) => {
      const rowNumber = index + 2; // account for header row
      const workdayId = normalizeStaffId(row[3]);
      const project = row[4] || '';
      const shiftType = row[5] && row[5].toLowerCase().includes('night') ? 'Night' : 'Day';
      const dietNotes = row[20] || '';
      const timestamp = row[1] || new Date().toISOString();
      const weekStartDate = getWeekStartDate(timestamp);
      const baseIndex = shiftType === 'Night' ? 13 : 6;
      const meals = {};
      dayKeys.forEach((key, offset) => {
        meals[key] = row[baseIndex + offset] || null;
      });
      const sheetRowId = crypto
        .createHash('sha1')
        .update(JSON.stringify({ rowNumber, workday: row[3] || '', project, shiftType, weekStartDate }))
        .digest('hex');
      return {
        sheet_row_id: sheetRowId,
        staff_id: workdayId,
        project,
        shift_type: shiftType,
        diet_notes: dietNotes,
        week_start_date: weekStartDate,
        ...meals
      };
    })
    .filter((row) => row && row.staff_id);
}

function normalizeStaffId(value) {
  if (value === undefined || value === null) {
    return null;
  }
  const trimmed = String(value).trim();
  if (trimmed === '') {
    return null;
  }
  const digits = trimmed.match(/\d+/g);
  if (!digits) {
    return null;
  }
  const joined = digits.join('');
  const parsed = Number.parseInt(joined, 10);
  if (!Number.isFinite(parsed)) {
    return null;
  }
  return parsed;
}

async function getExistingRowIds(connection) {
  const [rows] = await connection.query('SELECT sheet_row_id FROM meal_selection');
  return new Set(rows.map((row) => row.sheet_row_id));
}

export function diffNewRows(existingIds, normalizedRows) {
  return normalizedRows.filter((row) => !existingIds.has(row.sheet_row_id));
}

async function getDbConnection() {
  const connection = await mysql.createConnection({
    host: process.env.DB_HOST || '127.0.0.1',
    port: Number(process.env.DB_PORT || 3306),
    user: process.env.DB_USERNAME || 'root',
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_DATABASE || 'canteen_db',
    charset: 'utf8mb4'
  });
  return connection;
}

async function insertRows(connection, rows) {
  if (!rows.length) {
    return { inserted: 0 };
  }
  let inserted = 0;
  for (const row of rows) {
    const [result] = await connection.execute(
      `INSERT INTO meal_selection (staff_id, project, shift_type, mon, tue, wed, thu, fri, sat, sun, diet_notes, sheet_row_id, week_start_date)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
       ON DUPLICATE KEY UPDATE project=VALUES(project), shift_type=VALUES(shift_type), mon=VALUES(mon), tue=VALUES(tue), wed=VALUES(wed), thu=VALUES(thu), fri=VALUES(fri), sat=VALUES(sat), sun=VALUES(sun), diet_notes=VALUES(diet_notes), week_start_date=VALUES(week_start_date)`
    , [
      row.staff_id,
      row.project,
      row.shift_type,
      row.mon,
      row.tue,
      row.wed,
      row.thu,
      row.fri,
      row.sat,
      row.sun,
      row.diet_notes,
      row.sheet_row_id,
      row.week_start_date
    ]);
    inserted += result.affectedRows > 0 ? 1 : 0;
  }
  return { inserted };
}

async function fetchKnownStaffIds(connection) {
  const [rows] = await connection.query('SELECT userid FROM user');
  return new Set(rows.map((row) => Number.parseInt(row.userid, 10)).filter((value) => Number.isFinite(value)));
}

export function annotateRowsWithStaffAvailability(rows, knownStaffIds) {
  return rows.map((row) => ({
    ...row,
    has_user: knownStaffIds.has(Number.parseInt(row.staff_id, 10))
  }));
}

async function main() {
  const mode = process.argv.includes('--import') ? 'import' : process.argv.includes('--preview') ? 'preview' : null;
  if (!mode) {
    console.log(JSON.stringify({ error: 'Specify --preview or --import' }));
    return;
  }
  try {
    const values = await fetchRows();
    const normalized = normalizeRows(values);
    const connection = await getDbConnection();
    try {
      const existing = await getExistingRowIds(connection);
      const newRows = diffNewRows(existing, normalized);
      const knownStaff = await fetchKnownStaffIds(connection);
      const annotatedRows = annotateRowsWithStaffAvailability(newRows, knownStaff);
      if (mode === 'preview') {
        console.log(JSON.stringify(annotatedRows));
      } else {
        const eligibleRows = annotatedRows.filter((row) => row.has_user);
        const skippedRows = annotatedRows
          .filter((row) => !row.has_user)
          .map((row) => ({ sheet_row_id: row.sheet_row_id, staff_id: row.staff_id }));
        const result = await insertRows(connection, eligibleRows);
        console.log(
          JSON.stringify({
            inserted: result.inserted,
            totalNew: annotatedRows.length,
            attempted: eligibleRows.length,
            skippedMissingStaff: skippedRows
          })
        );
      }
    } finally {
      await connection.end();
    }
  } catch (error) {
    console.log(JSON.stringify({ error: error.message }));
  }
}

const invokedDirectly = (() => {
  if (!process.argv[1]) {
    return false;
  }
  try {
    const invokedBasename = path.basename(process.argv[1]).toLowerCase();
    const currentBasename = path.basename(__filename).toLowerCase();
    if (invokedBasename !== currentBasename) {
      return false;
    }
    const resolvedInvoked = fs.realpathSync(process.argv[1]);
    const resolvedCurrent = fs.realpathSync(__filename);
    return resolvedInvoked.toLowerCase() === resolvedCurrent.toLowerCase();
  } catch (error) {
    // If realpath fails (e.g. script invoked with virtual path), fall back to basename match.
    return path.basename(process.argv[1]).toLowerCase() === path.basename(__filename).toLowerCase();
  }
})();

if (invokedDirectly) {
  main();
<<<<<<< ours
<<<<<<< ours
}
=======
}
>>>>>>> theirs
=======
}
>>>>>>> theirs
