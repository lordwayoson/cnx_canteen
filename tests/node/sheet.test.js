import assert from 'node:assert';
import { diffNewRows, normalizeRows, annotateRowsWithStaffAvailability } from '../../google-sync/index.js';

const header = ['Email Address', 'Timestamp', 'Extra', 'Workday ID', 'Project', 'Type of Shift', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun', 'Mon Night', 'Tue Night', 'Wed Night', 'Thu Night', 'Fri Night', 'Sat Night', 'Sun Night', 'Dietary Restrictions'];
const sampleRows = [
  header,
  ['john@example.com', '2024-06-01T08:00:00Z', '', '1001', 'Project Alpha', 'Day', 'Meal1', 'Meal2', 'Meal3', 'Meal4', 'Meal5', 'Meal6', 'Meal7', '', '', '', '', '', '', '', 'Nut-free'],
  ['ama@example.com', '2024-06-01T08:00:00Z', '', 'WD-1002', 'Project Beta', 'Night Shift', '', '', '', '', '', '', '', 'Night1', 'Night2', 'Night3', 'Night4', 'Night5', 'Night6', 'Night7', 'Vegetarian'],
  ['yaw@example.com', '14/06/2024 07:30:00', '', '1003', 'Project Gamma', 'Day', 'GMeal1', 'GMeal2', 'GMeal3', 'GMeal4', 'GMeal5', 'GMeal6', 'GMeal7', '', '', '', '', '', '', '', 'Halal'],
  ['kofi@example.com', '2024-06-01T08:00:00Z', '', '', 'Project Delta', 'Day', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '']
];

const normalized = normalizeRows(sampleRows);

assert.strictEqual(normalized.length, 3, 'Rows without Workday ID are dropped');
assert.strictEqual(normalized[0].shift_type, 'Day');
assert.strictEqual(normalized[0].mon, 'Meal1');
assert.strictEqual(normalized[1].shift_type, 'Night');
assert.strictEqual(normalized[1].tue, 'Night2');
assert.strictEqual(normalized[1].staff_id, 1002);
assert.strictEqual(normalized[2].week_start_date.length, 10, 'Week start date should be formatted YYYY-MM-DD');

const existing = new Set([normalized[0].sheet_row_id]);
const newRows = diffNewRows(existing, normalized);
assert.strictEqual(newRows.length, 2, 'Two new rows should remain after diff');

const availability = annotateRowsWithStaffAvailability(normalized, new Set([normalized[0].staff_id, normalized[2].staff_id]));
assert.strictEqual(availability[0].has_user, true, 'Known staff remains importable');
assert.strictEqual(availability[1].has_user, false, 'Unknown staff should be flagged');
assert.strictEqual(availability[2].has_user, true, 'Second known staff is available');

<<<<<<< ours
<<<<<<< ours
console.log('All sheet tests passed');
=======
console.log('All sheet tests passed');
>>>>>>> theirs
=======
console.log('All sheet tests passed');
>>>>>>> theirs
