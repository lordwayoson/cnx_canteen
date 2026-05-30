#!/usr/bin/env bash
set -euo pipefail

OUTPUT_DIR="${1:-$HOME/backups/canteen}"
MYSQLDUMP_BIN="${MYSQLDUMP_BIN:-mysqldump}"
HOST="${DB_HOST:-127.0.0.1}"
USER="${DB_USER:-root}"
PASSWORD="${DB_PASSWORD:-}"
DATABASE="canteen_db"

mkdir -p "$OUTPUT_DIR"
TIMESTAMP=$(date +"%Y%m%d-%H%M%S")
FILENAME="$OUTPUT_DIR/${DATABASE}-${TIMESTAMP}.sql"

$MYSQLDUMP_BIN --host="$HOST" --user="$USER" --password="$PASSWORD" --routines --events --single-transaction "$DATABASE" > "$FILENAME"

<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
echo "Backup created at $FILENAME"
=======
echo "Backup created at $FILENAME"
>>>>>>> theirs
=======
echo "Backup created at $FILENAME"
>>>>>>> theirs
=======
echo "Backup created at $FILENAME"
>>>>>>> theirs
