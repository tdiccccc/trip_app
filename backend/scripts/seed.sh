#!/bin/sh
set -e

# ============================================================
# Cloud Run Jobs entrypoint for seeding operations
#
# Environment variables:
#   SEED_MODE       — "all", "master", "dummy", or "reset"
#   LITESTREAM_REPLICA_BUCKET — GCS bucket for Litestream
# ============================================================

DB_PATH=/app/database/database.sqlite
CONFIG=/app/litestream.yml

echo "[seed] Mode: ${SEED_MODE}"
echo "[seed] DB path: ${DB_PATH}"

# ============================================================
# Step 1: Restore database from GCS
# ============================================================
echo "[seed] Removing existing local database..."
rm -f "$DB_PATH"

echo "[seed] Restoring database from GCS..."
litestream restore -if-replica-exists -o "$DB_PATH" -config "$CONFIG" "$DB_PATH"

if [ ! -f "$DB_PATH" ]; then
    echo "[seed] No replica found. Creating empty database..."
    touch "$DB_PATH"
fi

echo "[seed] Restore complete."

# ============================================================
# Step 2: Run seed command based on mode
# ============================================================
case "$SEED_MODE" in
    reset)
        echo "[seed] Running migrate:fresh --seed (full reset)..."
        php artisan migrate:fresh --seed --force
        ;;
    all)
        echo "[seed] Running migrations..."
        php artisan migrate --force
        echo "[seed] Running all seeders (DatabaseSeeder)..."
        php artisan db:seed --force
        ;;
    master)
        echo "[seed] Running migrations..."
        php artisan migrate --force
        echo "[seed] Running master data seeder (ExpenseCategorySeeder)..."
        php artisan db:seed --class=ExpenseCategorySeeder --force
        ;;
    dummy)
        echo "[seed] Running migrations..."
        php artisan migrate --force
        echo "[seed] Running dummy data seeders..."
        php artisan db:seed --class=UserSeeder --force
        php artisan db:seed --class=TripSeeder --force
        php artisan db:seed --class=TripMemberSeeder --force
        php artisan db:seed --class=SpotSeeder --force
        php artisan db:seed --class=SpotMemoSeeder --force
        php artisan db:seed --class=ItineraryItemSeeder --force
        php artisan db:seed --class=PhotoSeeder --force
        php artisan db:seed --class=BoardPostSeeder --force
        php artisan db:seed --class=ReactionSeeder --force
        php artisan db:seed --class=PackingItemSeeder --force
        php artisan db:seed --class=ExpenseSeeder --force
        ;;
    *)
        echo "[seed] ERROR: Unknown SEED_MODE '${SEED_MODE}'"
        exit 1
        ;;
esac

echo "[seed] Seed operation complete."

# ============================================================
# Step 3: Replicate back to GCS
# ============================================================
echo "[seed] Replicating database back to GCS..."
# Run Litestream replicate for a short period to push all changes.
# Using a subprocess with timeout to ensure it syncs then exits.
litestream replicate -config "$CONFIG" &
LITESTREAM_PID=$!

# Wait for sync to complete (sync-interval is 1s, wait 10s to be safe)
echo "[seed] Waiting 10 seconds for replication sync..."
sleep 10

echo "[seed] Stopping Litestream..."
kill "$LITESTREAM_PID" 2>/dev/null || true
wait "$LITESTREAM_PID" 2>/dev/null || true

echo "[seed] Replication complete. Job finished successfully."
