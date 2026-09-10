# Concentrix Ghana Canteen Management System

A locally hosted canteen management system built with PHP 8.2, MySQL, Bootstrap, and a Node.js Google Sheets intake utility.

## Quick start on XAMPP

1. Copy or clone the project into `C:\xampp\htdocs\cnx_canteen`.
2. Copy `.env.example` to `.env`.
3. Set `APP_BASE_PATH=/cnx_canteen`.
4. Create the database and seed data:

```bat
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS canteen_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p canteen_db < scripts\init-db.sql
mysql -u root -p canteen_db < scripts\seed-demo.sql
```

5. Install dependencies:

```bat
cd backend
composer install
cd ..\google-sync
npm install
```

6. Open either URL:

- `http://localhost/cnx_canteen/`
- `http://localhost/cnx_canteen/frontend/`

If `localhost` returns a Microsoft-IIS 404, port 80 is being served by IIS/HTTP.sys instead of XAMPP Apache. Stop the IIS site/service or move Apache to another port, then restart Apache from the XAMPP Control Panel.

To repair this automatically on Windows, open PowerShell as Administrator and run:

```powershell
cd C:\xampp\htdocs\Canteen\canteen-system
.\scripts\fix-localhost-cnx-canteen.ps1
```

The script stops IIS services using port 80, ensures `C:\xampp\htdocs\cnx_canteen` points to this project, starts XAMPP Apache, and verifies `http://localhost/cnx_canteen/`.

Seeded logins:

- Admin: `admin` / `admin123!`
- Kitchen: `kitchen` / `kitchen123!`

Change seeded passwords before real use.

## Google Sheets credentials

Do not commit Google service account keys.

To configure Google Sheets locally:

1. Create a new service account key in Google Cloud Console only if the app requires direct Sheets API access.
2. Save the downloaded JSON file as `storage/google-service-account.json`.
3. Set `GOOGLE_APPLICATION_CREDENTIALS=storage/google-service-account.json` in `.env`.
4. Set `GOOGLE_SHEETS_ID` in `.env`.
5. Share the Google Sheet with the service account email.

If the credential file is missing, the dashboard preview/import API returns a setup error instead of crashing.

## Emergency response after leaked Google service account key

1. Disable/delete the exposed key in Google Cloud IAM.
2. Create a new key only if required.
3. Restrict the service account to the minimum required Google Sheets permissions.
4. Remove the leaked file from Git history using `git filter-repo` or BFG Repo-Cleaner.
5. Force-push the cleaned repository.
6. Add GitHub secret scanning and check `git status` before every push.

Example cleanup commands are included in the deployment handoff/fix notes.

## Configuration

Important `.env` values:

- `APP_BASE_PATH=/cnx_canteen`
- `APP_BASE_URL=http://localhost/cnx_canteen`
- `GOOGLE_APPLICATION_CREDENTIALS=storage/google-service-account.json`
- `GOOGLE_SHEETS_ID=140X4I0IKzxc7tGwkH0VRiZ-q3UuCYRr2f5OlKczlJKo`
- `DB_*` for the local canteen database
- `INGRESS_DB_*` for the FingerTec/Ingress source database
- `NODE_BINARY` if Apache/PHP cannot locate Node.js

## Project structure

```text
cnx_canteen/
  backend/      PHP REST API, models, and libraries
  config/       Shared app path and environment helpers
  frontend/     Login, dashboard, reports, kitchen queue
  google-sync/  Google Sheets intake utility
  scripts/      Database setup and backup scripts
  storage/      Local ignored runtime files
  tests/        Smoke tests and API collections
```

## Testing

```bat
cd backend
vendor\bin\phpunit

cd ..\google-sync
npm test
```

Manual checks:

- Login page loads.
- Dashboard loads after login.
- Google Sheet preview returns a friendly setup error when credentials are missing.
- Kitchen queue page loads.
- API endpoints under `backend/api/` are reachable.

## Security notes

- Passwords are hashed with `password_hash()`.
- Session cookies are HTTP-only and use `SameSite=Lax`.
- Database access uses PDO prepared statements.
- `.env` and credential JSON files are ignored by Git.
