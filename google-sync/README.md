# Google Sheets Intake Utility

This Node.js service fetches meal selections captured through Google Forms and synchronises only new entries into the local `canteen_db.meal_selection` table.

## Setup

1. Copy `.env.example` from the repository root to `.env` and populate Google credentials, database connection, and timezone.
<<<<<<< ours
<<<<<<< ours
2. Place the Google service account JSON file at the path referenced by `GOOGLE_SERVICE_JSON`, or paste the JSON content directly into that variable.
=======
2. Place the Google service account JSON file in `storage/service-account.json` (the default path) or update `GOOGLE_SERVICE_JSON` to point to an absolute path, inline JSON, or a `base64:`-prefixed payload.
>>>>>>> theirs
=======
2. Place the Google service account JSON file in `storage/service-account.json` (the default path) or update `GOOGLE_SERVICE_JSON` to point to an absolute path, inline JSON, or a `base64:`-prefixed payload.
>>>>>>> theirs
3. Install dependencies:
   ```bash
   npm install
   ```

## Usage

- **Preview only new rows**:
  ```bash
  npm run preview
  ```
  Outputs a JSON array of rows that are not yet stored in MySQL.

- **Import new rows**:
  ```bash
  npm run import
  ```
  Inserts new records into `meal_selection` using `sheet_row_id` for deduplication.

- **Run tests**:
  ```bash
  npm test
  ```

## Notes

- The sheet range defaults to `Form Responses 1!A1:U` to cover all defined columns.
- Shift-specific meals are mapped from day (G–M) or night (N–T) columns depending on `Type of Shift`.
<<<<<<< ours
<<<<<<< ours
- Week start date is derived from the Monday of the submitted timestamp week to aid reporting.
=======
- Week start date is derived from the Monday of the submitted timestamp week to aid reporting.
>>>>>>> theirs
=======
- Week start date is derived from the Monday of the submitted timestamp week to aid reporting.
>>>>>>> theirs
