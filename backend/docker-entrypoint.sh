#!/bin/sh
set -e

DB_PATH=/app/database/database.sqlite

# ============================================================
# Litestream restore (if replica exists)
# ============================================================
if [ -n "$LITESTREAM_REPLICA_BUCKET" ]; then
    echo "[entrypoint] Restoring database from GCS (if replica exists)..."
    litestream restore -if-replica-exists -o "$DB_PATH" -config /app/litestream.yml "$DB_PATH"
    echo "[entrypoint] Restore complete."
fi

# ============================================================
# Ensure SQLite file exists (first deploy or empty replica)
# ============================================================
if [ ! -f "$DB_PATH" ]; then
    touch "$DB_PATH"
fi

# ============================================================
# Run migrations
# ============================================================
echo "[entrypoint] Running migrations..."
php artisan migrate --force

# ============================================================
# Start Litestream replicate + Laravel serve
# ============================================================
if [ -n "$LITESTREAM_REPLICA_BUCKET" ]; then
    echo "[entrypoint] Starting Litestream replication + Laravel..."
    exec litestream replicate -exec "php artisan serve --host=0.0.0.0 --port=${PORT}" -config /app/litestream.yml
else
    echo "[entrypoint] Starting Laravel (no Litestream)..."
    exec php artisan serve --host=0.0.0.0 --port="${PORT}"
fi
