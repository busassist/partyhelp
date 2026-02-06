#!/usr/bin/env bash
# Full reset and seed: drop all tables, re-run migrations, then seed.
# Use this for a clean slate with sample data.
# Run from project root, or this script will cd there automatically.

set -e
cd "$(dirname "$0")/.."

echo "Resetting and seeding database..."
php artisan migrate:fresh --seed

echo "Done. Database reset and seeded with sample data."
