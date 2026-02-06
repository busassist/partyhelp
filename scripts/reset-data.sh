#!/usr/bin/env bash
# Reset the database: drop all tables and re-run migrations.
# Leaves the database empty (no seed data).
# Run from project root, or this script will cd there automatically.

set -e
cd "$(dirname "$0")/.."

echo "Resetting database (migrate:fresh)..."
php artisan migrate:fresh

echo "Done. Database reset complete. Tables recreated, no data."
echo "Run ./scripts/seed-data.sh to populate with sample data."
