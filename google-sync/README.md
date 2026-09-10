# Google Sheets Intake Utility

This Node.js utility reads Google Form intake rows from Google Sheets and synchronizes only new rows into `canteen_db.meal_selection`.

Setup:

1. Copy `.env.example` to `.env`.
2. Create a new Google service account key in Google Cloud Console only after disabling any leaked key.
3. Save the key locally as `storage/google-service-account.json`.
4. Set `GOOGLE_APPLICATION_CREDENTIALS=storage/google-service-account.json`.
5. Set `GOOGLE_SHEETS_ID` and share the Sheet with the service account email.

Run from `google-sync/`:

```bash
npm install
npm test
npm run preview
```

Credential JSON files are ignored by Git and must stay local to the deployment machine.
