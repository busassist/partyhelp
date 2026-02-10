# Database Seeding

Scripts and seeders for populating the Partyhelp database with sample data.

## Quick Reference

| Script | Purpose |
|--------|---------|
| `./scripts/seed-data.sh` | Seed sample data only (requires existing schema) |
| `./scripts/reset-data.sh` | Drop all tables, re-run migrations (empty database) |
| `./scripts/reset-and-seed.sh` | Full reset + seed in one command |

## Scripts

### seed-data.sh

Populates the database with sample data. **Requires migrations to have been run** (schema must exist).

```bash
./scripts/seed-data.sh
```

Or with explicit path:

```bash
bash /home/forge/get.partyhelp.com.au/scripts/seed-data.sh
```

**What it seeds:**
- Admin user: `admin@partyhelp.com.au` / `adminfestive`
- Test venue: `venue@partyhelp.com.au` / `venuefestive` with 4 sample rooms (Main Function Room, The Loft, Courtyard, Boardroom)
- Occasion types (from config)
- Budget ranges ($1,500–$3,000 through $25,000+)
- Postcodes (Melbourne suburbs)
- 50 Melbourne-style venues (e.g. The Fox & Hound, Town Hall Richmond)
- 2–4 rooms per venue (Mezzanine Bar, Corporate Lounge, etc.)
- 80 leads with varied statuses (new, distributed, fulfilled, expired, etc.)
- Lead matches and purchases (with credit transactions)
- Pricing matrix, discount settings, system settings

**Note:** Running `seed-data.sh` on an already-seeded database will create duplicate or conflicting records. Use `reset-and-seed.sh` for a clean refresh.

### reset-data.sh

Drops all tables and re-runs migrations. Leaves the database empty.

```bash
./scripts/reset-data.sh
```

Use this when you want to clear all data but keep the schema. Run `seed-data.sh` afterwards to repopulate.

### reset-and-seed.sh

Performs a full reset and re-seeds in one command. Equivalent to `reset-data.sh` followed by `seed-data.sh`.

```bash
./scripts/reset-and-seed.sh
```

Use this when you want a clean slate with fresh sample data.

## Artisan Commands

The scripts wrap Laravel artisan commands. You can run them directly:

```bash
# Seed only
php artisan db:seed

# Reset only (migrate:fresh)
php artisan migrate:fresh

# Reset + seed
php artisan migrate:fresh --seed

# Run specific seeder
php artisan db:seed --class=OccasionTypeSeeder
php artisan db:seed --class=FeatureSeeder
php artisan db:seed --class=BudgetRangeSeeder
php artisan db:seed --class=PostcodeSeeder
php artisan db:seed --class=RoomSeeder
php artisan db:seed --class=VenueSeeder
php artisan db:seed --class=LeadSeeder
```

## Individual Seeders

| Seeder | Class | Purpose |
|--------|-------|---------|
| DatabaseSeeder | `DatabaseSeeder` | Core data (admin, test venue, pricing, config) + calls OccasionTypeSeeder, BudgetRangeSeeder, PostcodeSeeder, RoomSeeder, VenueSeeder, LeadSeeder |
| OccasionTypeSeeder | `OccasionTypeSeeder` | Occasion types from config (21st Birthday, Wedding Reception, etc.) |
| FeatureSeeder | `FeatureSeeder` | Room features (AV Equipment, Dance Floor, etc.) |
| VenueStyleSeeder | `VenueStyleSeeder` | Venue styles (Bar, Function Room, Night Club, etc.) + attaches 2–3 styles to existing venues |
| BudgetRangeSeeder | `BudgetRangeSeeder` | Budget ranges ($1,500–$3,000 through $25,000+) |
| PostcodeSeeder | `PostcodeSeeder` | Postcodes (suburb, postcode, state) for Melbourne |
| RoomSeeder | `RoomSeeder` | 4 sample rooms for venue@partyhelp.com.au (Main Function Room, The Loft, Courtyard, Boardroom). Skips if venue already has rooms. |
| VenueSeeder | `VenueSeeder` | 50 venues with rooms |
| LeadSeeder | `LeadSeeder` | 80 leads with matches and purchases |

## Making Scripts Executable

On first use, you may need to make the scripts executable:

```bash
chmod +x scripts/seed-data.sh
chmod +x scripts/reset-data.sh
chmod +x scripts/reset-and-seed.sh
```

## Environment

- **Development:** All work happens on Laravel Forge via Remote SSH (Digital Ocean).
- **Database:** MySQL (configured in `.env`).
- **Caution:** `reset-data.sh` and `reset-and-seed.sh` will **destroy all data** in the database. Do not run on production.
