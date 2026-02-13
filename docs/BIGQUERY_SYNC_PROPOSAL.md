# BigQuery Data Sync – Proposed Data Structures

**Purpose:** Sync Partyhelp platform data to BigQuery every 24 hours for reporting. Structures are optimised for analytics with **flattened**, join-free views where possible, especially for **sales and billing**.

**Sync strategy:** Daily scheduled job (e.g. once per 24h). Recommended: **full snapshot** of reporting tables per run (replace or merge by `sync_date`), so each run is self-contained and queries can filter by `sync_date` for point-in-time reporting.

---

## 1. Source system summary (relevant for sync)

| Area | Tables / concepts |
|------|-------------------|
| **Leads** | `leads` (occasion_type, guest_count, preferred_date, suburb, status, base_price, current_price, discount_percent, distributed_at, fulfilled_at, expires_at, purchase_count, purchase_target) |
| **Venues** | `venues` (business_name, area_id, status, credit_balance, contact_*, address, suburb, state, postcode) |
| **Sales / Billing** | `lead_purchases` (lead_id, venue_id, amount_paid, discount_percent, lead_status); `credit_transactions` (venue_id, type, amount, balance_after, lead_purchase_id, stripe_payment_intent_id) |
| **Matching** | `lead_matches` (lead_id, venue_id, status, notified_at, purchased_at) |
| **Reference** | `occasion_types` (key → label), `areas` (id → name), `rooms` (venue_id, name, style), config `room_styles` |

**Event concept (for reporting):** A “lead” represents a customer event request. Key reporting attributes:

- **Event type:** `occasion_type` (key) + human **occasion_label** (e.g. "21st Birthday", "Wedding Reception")
- **Event date:** `preferred_date`
- **Event size:** `guest_count` (and optional `guest_count_display` for range strings)
- **Location:** `suburb`, and optionally `suburb_preferences` / `location_selections` (we can flatten to a single location string for BQ)

---

## 2. Recommended BigQuery dataset and naming

- **Dataset name:** `partyhelp` (or `partyhelp_reporting` if you prefer to separate from future raw syncs).
- **Table prefix:** `ph_` to avoid clashes and make source clear.
- **Sync metadata:** Each table includes `sync_date` (DATE) or `synced_at` (TIMESTAMP) indicating when the row was written by the sync job.

---

## 3. Proposed tables (reporting-optimised)

### 3.1 Dimension-style tables (one row per entity, refreshed daily)

Use for filters, dimensions in reports, and “as at” snapshots.

#### **ph_leads**

One row per lead. Denormalised with human-readable labels where useful.

| Column | Type | Description |
|--------|------|-------------|
| lead_id | INT64 | PK (source `leads.id`) |
| occasion_type | STRING | e.g. `21st_birthday`, `wedding_reception` |
| occasion_label | STRING | Human label, e.g. "21st Birthday" (from occasion_types or config) |
| guest_count | INT64 | Numeric guest count |
| preferred_date | DATE | Customer preferred event date |
| suburb | STRING | Primary suburb |
| status | STRING | new, distributed, partially_fulfilled, fulfilled, expired, cancelled |
| base_price | FLOAT64 | List price from pricing matrix |
| current_price | FLOAT64 | After discount |
| discount_percent | INT64 | Current discount % |
| purchase_target | INT64 | Fulfilment target (e.g. 3) |
| purchase_count | INT64 | Number of purchases |
| distributed_at | TIMESTAMP | When lead was distributed to venues |
| fulfilled_at | TIMESTAMP | When fulfilment target was met |
| expires_at | TIMESTAMP | Lead expiry |
| created_at | TIMESTAMP | Source created_at |
| sync_date | DATE | Day this row was synced |

**Optional (if you want location in one string):** `location_display` STRING (e.g. comma-separated preferred locations).

**PII note:** For reporting you may omit or hash `first_name`, `last_name`, `email`, `phone`; the proposal above keeps only event/aggregate fields. Add PII columns only if required for approved use cases.

---

#### **ph_venues**

One row per venue. Flattened with area name.

| Column | Type | Description |
|--------|------|-------------|
| venue_id | INT64 | PK (source `venues.id`) |
| business_name | STRING | |
| area_id | INT64 | Nullable |
| area_name | STRING | From `areas.name` (flattened) |
| suburb | STRING | |
| state | STRING | |
| postcode | STRING | |
| status | STRING | pending, active, inactive, suspended |
| credit_balance | FLOAT64 | As at sync |
| approved_at | TIMESTAMP | Nullable |
| last_activity_at | TIMESTAMP | Nullable |
| created_at | TIMESTAMP | Source created_at |
| sync_date | DATE | Day this row was synced |

---

### 3.2 Flattened sales / billing tables (primary for revenue reporting)

These are **flattened** so that event name, identifiers, and amounts are in one place—minimal or no joins needed for billing and sales reports.

#### **ph_lead_purchases** (core sales fact)

One row per lead purchase with **event context** and **venue context** in the same row.

| Column | Type | Description |
|--------|------|-------------|
| lead_purchase_id | INT64 | PK (source `lead_purchases.id`) |
| lead_id | INT64 | |
| venue_id | INT64 | |
| **Event / lead context (flattened)** | | |
| occasion_type | STRING | e.g. 21st_birthday |
| occasion_label | STRING | e.g. "21st Birthday" |
| preferred_date | DATE | Event date |
| guest_count | INT64 | |
| lead_suburb | STRING | |
| lead_status | STRING | contacted, quoted, booked, lost, pending |
| **Venue context (flattened)** | | |
| venue_business_name | STRING | |
| venue_area_name | STRING | |
| **Billing** | | |
| amount_paid | FLOAT64 | Revenue for this purchase |
| discount_percent | INT64 | Discount at time of purchase |
| **Timestamps & sync** | | |
| purchased_at | TIMESTAMP | Source lead_purchases.created_at (or purchased_at if you add it) |
| sync_date | DATE | Day this row was synced |

**Use case:** Revenue by event type, by venue, by date; conversion by occasion; average sale by occasion_label.

---

#### **ph_credit_transactions** (all credit movements)

One row per credit transaction. Flattened with venue and optional link to purchase.

| Column | Type | Description |
|--------|------|-------------|
| credit_transaction_id | INT64 | PK (source `credit_transactions.id`) |
| venue_id | INT64 | |
| venue_business_name | STRING | Flattened |
| type | STRING | topup, auto_topup, purchase, refund, adjustment |
| amount | FLOAT64 | Signed (e.g. negative for purchase) |
| balance_after | FLOAT64 | Venue balance after this tx |
| lead_purchase_id | INT64 | Nullable; set for type = 'purchase' |
| stripe_payment_intent_id | STRING | Nullable |
| description | STRING | Nullable |
| admin_note | STRING | Nullable |
| created_at | TIMESTAMP | Source created_at |
| sync_date | DATE | Day this row was synced |

**Use case:** Cash flow, top-ups vs spend, refunds; reconciliation with Stripe via `stripe_payment_intent_id`.

---

### 3.3 Optional: match and lead-metrics tables

#### **ph_lead_matches** (optional)

One row per lead–venue match. Useful for funnel (notified → viewed → purchased).

| Column | Type | Description |
|--------|------|-------------|
| lead_match_id | INT64 | PK |
| lead_id | INT64 | |
| venue_id | INT64 | |
| venue_business_name | STRING | Flattened |
| match_score | FLOAT64 | |
| status | STRING | notified, viewed, purchased, expired, declined |
| notified_at | TIMESTAMP | |
| viewed_at | TIMESTAMP | Nullable |
| purchased_at | TIMESTAMP | Nullable |
| sync_date | DATE | |

---

### 3.4 Single “billing summary” view (optional but recommended)

A **view** (or materialised table) that joins nothing at report time: one row per purchase with every identifier and event label you need for invoices, revenue reports, and dashboards.

**View name:** `ph_sales_billing_summary`  
**Source:** Same as `ph_lead_purchases`; can be a BigQuery VIEW over `ph_lead_purchases` or the same data written by the sync job.

Suggested columns (all from `ph_lead_purchases` plus any extras you want):

- `lead_purchase_id`, `lead_id`, `venue_id`
- **Event:** `occasion_label` (event name), `occasion_type`, `preferred_date`, `guest_count`
- **Venue:** `venue_business_name`, `venue_area_name`
- **Billing:** `amount_paid`, `discount_percent`, `lead_status`
- **Time:** `purchased_at`, `sync_date`

No extra tables required if `ph_lead_purchases` already has these; the “view” can simply be “query ph_lead_purchases” with a clear name in your BI tool or a BQ view that selects these columns.

---

## 4. Summary: what to sync and in what form

| BigQuery table | Source | Rows | Purpose |
|----------------|--------|------|---------|
| **ph_leads** | leads + occasion_types/config | One per lead | Lead/event dimensions; status and price snapshot |
| **ph_venues** | venues + areas | One per venue | Venue dimensions; balance snapshot |
| **ph_lead_purchases** | lead_purchases + leads + venues + areas + occasion label | One per purchase | **Flattened sales/billing** – event name, identifiers, amount, discount, status |
| **ph_credit_transactions** | credit_transactions + venues | One per tx | **Flattened credit movements** – type, amount, balance, optional lead_purchase_id |
| **ph_lead_matches** (optional) | lead_matches + venues | One per match | Funnel and match-level reporting |

**Sync cadence:** Once every 24 hours (scheduled task).  
**Sync approach:** Full refresh of these tables per run (e.g. truncate + insert, or merge on key + `sync_date`), with `sync_date = current_date()` so reports can use “data as at” a given day.

---

## 5. Confirm before implementation

Please confirm:

1. **Dataset name:** `partyhelp` vs `partyhelp_reporting` (or other).
2. **PII in BigQuery:** Should `ph_leads` (or any table) include customer name/email/phone, or keep only event/aggregate fields?
3. **Which tables to implement first:** e.g. `ph_lead_purchases` + `ph_credit_transactions` + `ph_venues` for billing, then add `ph_leads` and optionally `ph_lead_matches`.
4. **View:** Is a single “billing summary” view/table (as in 3.4) sufficient, or do you want additional flattened views (e.g. by financial period)?

Once you’ve reviewed and confirmed, the next step is to set up the Google BigQuery project (dataset, service account, permissions) and then implement the Laravel sync job and table schemas.
