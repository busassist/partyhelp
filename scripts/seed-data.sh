#!/usr/bin/env bash
# Seed the database with sample data (venues, leads, etc.)
# Run from project root, or this script will cd there automatically.

set -e
cd "$(dirname "$0")/.."

echo "Seeding database..."
php artisan db:seed

echo "Done. Database seeded with:"
echo "  - Admin user (admin@partyhelp.com.au)"
echo "  - Test venue (venue@partyhelp.com.au)"
echo "  - 50 Melbourne-style venues with 2-4 rooms each"
echo "  - 80 leads with matches and purchases"
echo "  - Pricing matrix, discount settings, system settings"
