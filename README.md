# Concentrix Ghana Canteen Management System

A production-ready, locally hosted canteen management solution featuring PHP 8.2 REST APIs, a Bootstrap 5 admin dashboard, RFID queue handling, Google Sheets intake, and comprehensive reporting for Concentrix Ghana.

## Features

- Secure session-based authentication with role-based access (`admin`, `kitchen`)
- Admin dashboard for ingress RFID sync, Google Sheets intake preview/import, and reporting
- Kitchen queue display with automatic refresh for real-time serving updates
- Google Sheets integration (preview + import of only new rows)
- RFID ingestion via Ingress database (FingerTec TA200)
- Reporting module with Chart.js, DataTables, and TCPDF PDF exports
- Docker Compose stack for MySQL + phpMyAdmin (optional)
- Daily backup scripts for Windows PowerShell and Linux/macOS shells
- PHPUnit smoke tests and Node unit tests for Sheet parsing logic

## Prerequisites

- Windows 11 with [XAMPP](https://www.apachefriends.org/) (PHP 8.2+, Apache, MySQL 8)
- [Node.js 18 LTS](https://nodejs.org/) and npm
- [Composer](https://getcomposer.org/)
- Optional: Docker Desktop for running the bundled Docker Compose stack

## Quick Start

1. **Clone or copy** this repository to `C:\xampp\htdocs\canteen-system` (or preferred location).
2. **Create databases** using MySQL shell or XAMPP shell:
   ```bat
   mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS canteen_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   mysql -u root -p canteen_db < scripts\init-db.sql
   mysql -u root -p canteen_db < scripts\seed-demo.sql
   ```
3. **Copy environment file**:
   ```bat
   copy .env.example .env
   ```
   Fill in MySQL root password (if any), ingress credentials, and Google Sheets details.
   Place your Google service account JSON in `storage/service-account.json`. Leaving `GOOGLE_SERVICE_JSON` blank automatically falls back to that location, or set it to an absolute path/base64 value if you store the credential elsewhere.
4. **Install PHP dependencies**:
   ```bat
   cd backend
   composer install
   cd ..
   ```
5. **Install Node dependencies**:
   ```bat
   cd google-sync
   npm install
   npm run test
   npm run preview
   cd ..
   ```
6. **Configure Apache**:
   - Set the DocumentRoot to `canteen-system/frontend` or access via `http://localhost/canteen-system/frontend/`
   - Ensure `mod_rewrite` is enabled (default in XAMPP)
7. **Login** using the seeded credentials:
   - Admin: `admin` / `admin123!`
   - Kitchen: `kitchen` / `kitchen123!`
   > **Important:** change these passwords after first login.
8. **Ingress Sync**: In the dashboard, click **"Ingress Sync"** to import staff and card numbers from the FingerTec `ingress` database.
9. **Sheet Preview → Import**: Use **"Google Sheet Preview"** to fetch only new rows from the Google Form intake, review them, and click **"Confirm Import"** to persist.
10. **Simulate RFID Serve**: Issue a POST request to `backend/api/queue/serve.php` with `cardnumber` or `userid` to simulate a scan.
11. **Kitchen Queue Display**: Open `frontend/queue.php` on a dedicated kitchen screen for live updates.
12. **Reports**: Navigate to `frontend/reports/index.php` to filter served meals, view charts, and export to PDF via TCPDF.

## Environment Variables

See `.env.example` for the complete list. Key settings include:

- `DB_*` for primary `canteen_db` connection
- `INGRESS_DB_*` for the FingerTec `ingress` database
- `GOOGLE_SHEETS_ID` and `GOOGLE_SERVICE_JSON` for Google Sheets API access. The JSON can be
  - left blank to use the default `storage/service-account.json` file,
  - a relative path,
  - an absolute path,
  - inline JSON, or
  - a base64 payload prefixed with `base64:`.
- Optional helpers: set `GOOGLE_SHEETS_RANGE` (e.g. `Intake!A1:U`), `GOOGLE_SHEETS_TITLE`, or `GOOGLE_SHEETS_GID` when your Sheet tab is not named **Form Responses 1**; adjust `GOOGLE_SHEETS_COLUMNS` if you collect additional columns.
- `NODE_BINARY` when Apache/PHP cannot find Node.js on the PATH (e.g. `C:\\Program Files\\nodejs\\node.exe`).
- `CORS_ALLOWED_ORIGINS` for LAN-restricted access to REST endpoints

## Docker Compose (Optional)

A `docker-compose.yml` is provided to spin up MySQL 8 and phpMyAdmin locally. Adjust ports as needed, then run `docker compose up -d`. Update `.env` to point to the container database credentials.

## Project Structure

```
canteen-system/
├─ frontend/           # Login, dashboard, kitchen queue, reports UI
├─ backend/            # REST API endpoints, models, shared libs
├─ google-sync/        # Node.js utility for Google Sheets intake
├─ scripts/            # Database init/seed scripts + backup helpers
├─ tests/              # PHPUnit + Postman + Node tests
└─ docker-compose.yml  # Optional MySQL + phpMyAdmin stack
```

## Testing

- **PHP**: Run `vendor/bin/phpunit` inside `backend/` (after `composer install`).
- **Node**: Run `npm test` inside `google-sync/` to validate sheet parsing.
- **Manual**: Import Postman collection `tests/Postman_Collection.json` to exercise endpoints.

## Troubleshooting

- **Cannot find module 'dotenv'**: Ensure `npm install` has been executed inside `google-sync/`. Delete `node_modules` and reinstall if necessary.
- **CORS errors**: Verify `CORS_ALLOWED_ORIGINS` is correctly set in `.env`. The shared CORS helper injects headers for same-LAN origins.
- **Timezone mismatch**: Set `date.timezone = Africa/Accra` in `php.ini` and ensure `process.env.TZ` is `Africa/Accra`. Restart Apache/Node processes.
- **Ingress mapping issues**: Confirm that `ingress.cardlog` contains card numbers for the test users. Adjust JOIN logic in `backend/api/ingress/importUsers.php` if your schema differs.
- **Sheet rows not importing**: `sheet_row_id` prevents duplicates. If preview returns "No new rows" despite new data, confirm the service account can read the Sheet and that `.env` specifies the correct tab via `GOOGLE_SHEETS_RANGE`, `GOOGLE_SHEETS_TITLE`, or `GOOGLE_SHEETS_GID`.
- **Tests report "Not run (Node/Google credentials unavailable in container)"**: Install the Node dependencies in `google-sync/` (`npm install`) and ensure `.env` points to a valid service-account JSON. When running inside automated environments, provide stub credentials or skip the preview/import tests.

## Security Notes

- Passwords are hashed using `password_hash()` with `PASSWORD_DEFAULT`.
- Sessions use a named cookie; regenerate session IDs on login for fixation protection.
- All database access uses PDO prepared statements, and inputs are validated/sanitized server-side.

## License

<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
This project is provided as-is for internal Concentrix Ghana deployment. Review with your legal/compliance team before production use.
=======
This project is provided as-is for internal Concentrix Ghana deployment. Review with your legal/compliance team before production use.
>>>>>>> theirs
=======
This project is provided as-is for internal Concentrix Ghana deployment. Review with your legal/compliance team before production use.
>>>>>>> theirs
=======
This project is provided as-is for internal Concentrix Ghana deployment. Review with your legal/compliance team before production use.
>>>>>>> theirs
