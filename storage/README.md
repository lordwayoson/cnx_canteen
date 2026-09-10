# Local Storage

This directory is for local runtime files that must not be committed.

For Google Sheets integration:

1. In Google Cloud Console, disable/delete any leaked service account key first.
2. Create a new service account key only if the app still needs one.
3. Save the downloaded key locally as `storage/google-service-account.json`.
4. Set `GOOGLE_APPLICATION_CREDENTIALS=storage/google-service-account.json` in `.env`.
5. Share the target Google Sheet with the service account email.

Never commit Google credential JSON files. The repository ignores `storage/*.json`.
