# Product Requirements Document
## Partyhelp Venues Pay-Per-Lead System

**Version:** 2.1  
**Date:** February 2026  
**Prepared for:** Partyhelp  
**Prepared by:** Business Assist  

---

## Table of Contents

1. [Executive Summary](#1-executive-summary)
2. [Product Overview](#2-product-overview)
3. [Front-End User Experience](#3-front-end-user-experience)
4. [Form Submission and Lead Distribution](#4-form-submission-and-lead-distribution)
5. [Lead Pricing Structure](#5-lead-pricing-structure)
6. [Email and SMS Specifications](#6-email-and-sms-specifications)
7. [Customer Engagement Sequences](#7-customer-engagement-sequences)
8. [Venue Portal](#8-venue-portal)
9. [Platform Admin Backend](#9-platform-admin-backend)
10. [Payment System](#10-payment-system)
11. [Technical Architecture](#11-technical-architecture)
12. [Non-Functional Requirements](#12-non-functional-requirements)
13. [Success Metrics](#13-success-metrics)
14. [Appendix](#14-appendix)
15. [Resolved Design Decisions](#15-resolved-design-decisions)

---

## 1. Executive Summary

This PRD specifies a **pay-per-lead marketplace** that connects party planners seeking venues with venue operators who pay for qualified function enquiries.

**Architecture:**
- **WordPress** site hosts lead-generation landing pages where customers submit venue/party requirements
- **Laravel/PHP/MySQL** backend receives leads, runs matching, manages emails/notifications, and handles commercial/operational processes

**Core value proposition:**
- Top **30 venues** matching an enquiry receive a lead opportunity
- First **3–5 venues** to purchase the lead get exclusive access to customer contact details
- **Credit-based** payment with automatic top-ups for frictionless purchasing

**Customer experience:** Align with partyhelp.com.au visual style, with visual room-style selection and function-space information. Automation handles venue follow-ups, discount incentives, and customer engagement to maximise conversions.

---

## 2. Product Overview

### 2.1 Problem Statement

- Venue operators struggle to acquire qualified function leads cost-effectively
- Party planners spend too much time researching and contacting venues
- Traditional advertising has uncertain ROI
- Existing directories do not deliver pre-qualified, intent-driven leads

### 2.2 Solution Overview

- **Intelligent matching** by location, capacity, and room style
- **Competitive distribution** to 30 venues with first-to-purchase priority
- **Credit-based payments** to remove per-transaction friction
- **Automated discount escalation** to improve lead fulfilment
- **Customer engagement sequences** including function packs for purchasing venues
- **Self-service venue portal** for profile and room management

### 2.3 Key Stakeholders

| Stakeholder | Role | Primary Goals |
|-------------|------|---------------|
| Venue Operators | Lead purchasers | Acquire qualified leads quickly at predictable costs |
| Party Planners | Lead originators | Receive proactive contact from genuinely interested venues |
| Partyhelp Admin | Platform operator | Generate revenue, maintain quality, optimise matching |

---

## 3. Front-End User Experience

### 3.1 Design Principles

*Note: Website currently being updated Feb 2026*

- Match partyhelp.com.au visual and functional style
- Existing colour scheme, typography, and brand elements
- Mobile-responsive design
- Navigation aligned with current menu items
- Testimonials and trust signals consistent with existing presentation

**Colour schemes by context:**

| Context | Scheme | Rationale |
|---------|--------|-----------|
| Partyhelp Admin portal | Light (white background) | Desktop-primary UI for system management |
| Venue self-service portal | Light (white background) | Mobile-friendly; light for admin-style workflows |
| End-customer (party planner) UI on app | Dark | Matches public WordPress partyhelp.com.au |

**Light scheme:** Purple headings and accents (lavender-fields palette below). **Dark scheme:** Align with partyhelp.com.au public site.

```json
{
  "lavender-fields": {
    "50": "#f8fafc", "100": "#f1f5f9", "200": "#e2e8f0",
    "300": "#cbd5e1", "400": "#a78bfa", "500": "#8b5cf6",
    "600": "#7c3aed", "700": "#6d28d9", "800": "#5b21b6"
  }
}
```

**CSS approach:** Use a framework that supports named semantic styles (e.g. `.primary-cta-button`) in addition to structural utility classes, so styling behaves more like a WordPress theme while retaining Tailwind-style utilities.

### 3.2 Enquiry Form Specifications

#### 3.2.1 Form Location and Access

- **Form hosted on WordPress** (Elementor) in the first instance; may be reviewed in future
- Accessible via "Send Me Party Venues" navigation and existing button placements
- URL structure: `/party-details/`

#### 3.2.2 Form Fields

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| First Name | Text | Yes | Min 2 characters |
| Last Name | Text | Yes | Min 2 characters |
| Email | Email | Yes | Valid format |
| Phone | Tel | Yes | Australian mobile/landline |
| Occasion Type | Dropdown | Yes | Predefined list |
| Number of People | Number | Yes | Min 10, max 500 |
| Preferred Date | Date picker | Yes | Future date only |
| Location/Suburb | Autocomplete | Yes | Melbourne suburbs (from public source, e.g. ABS) |
| Room Style | Photo selector | Yes | Min 1 selection |
| Budget Range | Dropdown | No | Configurable text values (e.g. 1500-3000, 3000-5000) |
| Special Requirements | Textarea | No | Max 500 characters |

#### 3.2.3 Room Style Photo Selector

Visual grid of clickable photos with labels:

| Style | Description |
|-------|-------------|
| Bar | Casual bar, standing room, bar atmosphere |
| Function Room | Dedicated private space, formal, seated dining |
| Pub | Traditional pub, relaxed, mixed seating |
| Club | Nightclub style, dance floor, modern lighting |
| Semi-Outdoor | Covered outdoor, beer garden, rooftop, courtyard |

- Multiple selections allowed
- Selected items highlighted (border/overlay)
- Photos: ~300×200px, high quality

#### 3.2.4 Occasion Type Options

21st, 30th, 40th, 50th, 60th, Other Birthday | Engagement Party | Wedding Reception | Corporate Function | Christmas Party | Farewell Party | Baby Shower | Other

---

## 4. Form Submission and Lead Distribution

### 4.1 WordPress → Laravel Integration (Webhook)

The Elementor WordPress form submits to the Laravel app via **webhook**:

- Laravel exposes a webhook endpoint to receive form payloads
- Elementor (or form plugin) configured to POST form data to the webhook URL on successful submission
- Webhook validates payload, maps fields to lead record, and processes as per 4.2
- Build must include webhook route, controller, validation, and field mapping
- Document webhook payload format and setup instructions for Elementor configuration

### 4.2 Submission Flow

1. WordPress form submits via webhook to Laravel
2. Form validation (server-side)
3. Lead created with status "New"
4. Matching engine identifies top 30 venues
5. Lead status → "Distributed"
6. Lead opportunity notifications (email + SMS) to all 30 venues
7. Customer receives confirmation email with expected response timeframe
8. System monitors purchases until fulfilment threshold

**Customer notifications:** Customer receives an email with venue details each time a venue purchases the lead (so early purchasers are not disadvantaged by slower venues). *Grouped time-window notifications may be introduced later.*

### 4.3 Venue Matching Algorithm

| Priority | Criterion | Logic |
|----------|-----------|-------|
| 1 (Highest) | Location | Suburb or adjacent suburbs |
| 2 | Capacity | Rooms accommodating guest count (20% buffer) |
| 3 | Room Style | Rooms matching selected style(s) |
| 4 | Occasion | Venues tagged for occasion type |
| 5 | Budget | Within budget range if specified (see 4.6) |

### 4.4 Lead Distribution Limits

| Parameter | Value |
|-----------|-------|
| Max venues receiving opportunity | 30 |
| Venues required for fulfilment | 3–5 (configurable) |
| Lead availability window | 72 hours |

### 4.5 Lead Fulfilment Process

- First venue to click "Purchase Lead" with sufficient credit receives the lead
- Purchase is instant when credit balance is sufficient
- Credit balance debited immediately
- When threshold reached, lead status → "Fulfilled"
- Remaining venues see "Lead No Longer Available"

### 4.6 Budget Matching (Initial Approach)

- Budget range on form: configurable text values passed in (e.g. `1500-3000`, `3000-5000`)
- Venues have rooms; each room has a room-hire cost that falls into a budget range
- Matching compares customer's selected budget range with venue room budget ranges
- Business rules to be refined later; build supports this structure from the start

---

## 5. Lead Pricing Structure

### 5.1 Pricing Matrix (Configurable)

| Occasion | 10–30 | 31–60 | 61–100 | 100+ |
|----------|-------|-------|--------|------|
| Birthday (21st–60th) | $8 | $12 | $18 | $25 |
| Engagement Party | $10 | $15 | $22 | $30 |
| Wedding Reception | $15 | $22 | $30 | $40 |
| Corporate Function | $12 | $18 | $25 | $35 |
| Christmas Party | $10 | $15 | $20 | $28 |
| Other Occasions | $6 | $10 | $15 | $20 |

### 5.2 Discount Escalation (Automation)

| Time Elapsed | Discount | Action |
|--------------|----------|--------|
| 24 hours | 10% off | Re-send to all 30 venues |
| 48 hours | 20% off | Re-send to all 30 venues |
| 72 hours | — | Lead expires, customer notified |

---

## 6. Email and SMS Specifications

### 6.1 Venue Communications

**Lead Opportunity Email**
- Subject: `New [Occasion] Lead - [Suburb] - [X] guests - $[Price]`
- Content: Occasion, guests, date, suburb, room style, price. No personal details.
- CTA: "Purchase This Lead - $[Price]"
- Footer: Credit balance, top-up link

**Lead Opportunity SMS**
- Short, scannable, link to purchase

### 6.2 Customer Communications

- Confirmation email on submission
- Venue introduction email each time a venue purchases (with venue details)
- Engagement sequence emails (see Section 7)

### 6.3 Email Architecture

- **Transactional/dynamic emails:** Built in Laravel, sent via SendGrid templating
- **General nurturing/EDM:** Keep in ActiveCampaign or Mailchimp; avoid complex dynamic data integration where possible

---

## 7. Customer Engagement Sequences

### 7.1 Sequence Overview

| Trigger | Timing | Purpose |
|---------|--------|---------|
| Confirmation | Immediate | Confirm submission |
| Venue intro | Per purchase | Introduce each purchasing venue |
| +8 hours | 8 hrs after submission | Prompt if no/few responses |
| +36 hours | 36 hrs | Shortlist check |
| +72 hours | 72 hrs | Additional services |

### 7.2 Implementation Approach

- Sequences built in Laravel (not external automation builder)
- Admin controls for lists, timing, and content
- Avoid building a generic automation builder; keep it purpose-built for Partyhelp

### 7.3 Function Packs

When venues purchase a lead, they receive **function packs** (documents for the 3–5 purchasing venues):

- **Format:** Binary files – primarily PDF, may be PPTX or other formats
- **Storage:** Server-side; emails contain **links** to hosted copies (not large attachments)
- **Hosted download page:** Secure page where venue can view and download; offer options (e.g. download as PDF, view in browser)
- App treats function packs as file attachments or links to server-side resources; links preferred to avoid large email attachments

---

## 8. Venue Portal

**Responsive design:** Venue portal must be usable on mobile. Desktop and mobile both supported.

### 8.1 Registration & Onboarding

- Registration form (business details, contact, ABN)
- Venue approval workflow: **"New venue for approval"** email sent to Partyhelp admin
- Email includes buttons: **Review vendor**, **Approve**, **Reject**
- Admin reviews and approves/rejects via email actions (or in-app)
- Minimum credit purchase ($100) to activate after approval

### 8.2 Venue Profile Management

- Business name, contact, ABN
- Website, address
- Suburb/region tags (own + adjacent)
- Occasion tags

### 8.3 Room Management (Up to 6 Rooms)

Per room: Name, style, min/max capacity, seated capacity, **room hire cost / budget range** (for budget matching), description (200 words), up to 4 images, features (AV, dance floor, private bar, outdoor access, etc.)

### 8.4 Image Management

- Up to 10 venue images
- Drag-and-drop ordering
- First image = hero in customer emails
- JPG/PNG, min 1200×800px, max 5MB
- Optimisation on upload

### 8.5 Lead Management

- **Available leads:** List of matching opportunities, sortable, one-click purchase, time remaining
- **Purchased leads:** History with customer details, status (Contacted, Quoted, Booked, Lost), notes, CSV export

### 8.6 Payment Settings

- Add/update card (Stripe)
- Add/update bank account (BECS)
- View balance and transaction history
- Invoices/receipts
- Auto top-up: threshold (default $75), amount (default $50)

---

## 9. Platform Admin Backend

**Responsive design:** Admin portal assumes **desktop as primary UI** for managing system data. Mobile support secondary.

### 9.1 Dashboard

- Leads (month/week/today)
- Conversion rate, revenue, active venues
- Average time to fulfilment
- Customer booking rate
- Real-time activity feed (submissions, purchases, top-ups, alerts)
- **Low-match alerts:** When &lt; 10 venues match a lead, alert admin via **email + SMS** (refinable later)

### 9.2 Lead Management

- Filterable/searchable list
- Bulk actions (resend, change status, export)
- Detail view, status changes, audit log
- Manual override (fulfil, cancel, refund)

### 9.3 Venue Management

- List with search, filters, status (Active, Inactive, Pending Approval)
- Credit balance, last activity
- Approve/reject registrations
- Edit profile, view purchase/payment history
- Manual credit adjustments, messages, suspend

### 9.4 Pricing Configuration

- Full CRUD for occasion types and guest brackets
- Price per occasion/guest combination
- Preview calculator

### 9.5 Discount Settings

- Discount percentages and timing
- Enable/disable automation
- Promotional discount codes

### 9.6 Email Template Management

- WYSIWYG editor
- Merge fields
- Preview and test send
- Version history
- A/B testing for subject lines (where supported by SendGrid)

### 9.7 Reporting

- Lead volume, conversion, revenue, venue performance, customer journey
- Export to CSV/PDF
- Scheduled delivery, custom date ranges, comparisons
- Consider data feed to BigQuery/Looker Studio for advanced reporting

### 9.8 System Settings

- Email sender, SMS gateway, payment gateway
- Lead expiry, fulfilment threshold
- Matching weights, suburb/region definitions

---

## 10. Payment System

### 10.1 Credit-Based Model

| Parameter | Value |
|-----------|-------|
| Minimum balance | $100 (to receive opportunities) |
| Auto top-up trigger | Below $75 |
| Auto top-up amount | $50 (configurable) |
| Manual top-up | $50, $100, $200, $500, or custom |

### 10.2 Flow

1. Venue adds payment method
2. Initial $100 minimum to activate
3. Opportunities sent while balance > $0
4. Purchase debits balance immediately
5. Below $75 → auto $50 top-up
6. Failed top-up → notify venue, pause opportunities

### 10.3 Payment Gateway

**Stripe** (cards + BECS direct debit)

### 10.4 Refund Policy

| Scenario | Policy |
|----------|--------|
| Invalid lead (fake) | Full refund after verification |
| Duplicate (same customer, 30 days) | Full refund |
| Customer uncontactable | No refund |
| Customer already booked | No refund |
| Venue closes account | Refund remaining credit |

### 10.5 Reconciliation

- Daily Stripe vs internal ledger
- Monthly revenue reports
- Xero integration
- GST-inclusive pricing, tax invoices

---

## 11. Technical Architecture

### 11.1 Technology Stack

| Component | Technology |
|-----------|------------|
| Lead generation site | WordPress (Elementor) |
| Backend app | Laravel / PHP / MySQL |
| Admin portal | Filament Panel (`/admin`) — light theme, desktop-primary |
| Venue portal | Filament Panel (`/venue`) — light theme, mobile-responsive |
| Customer-facing pages | Blade + Livewire — dark theme, mobile-first |
| Blade templating | `<x-layout>` component tags (NOT `@include`) |
| Payment | Stripe (cards + BECS) |
| Transactional email | SendGrid (via API — DO blocks SMTP ports) |
| SMS | Twilio or MessageMedia |
| Accounting | Xero |
| Hosting | WordPress on Siteground; Laravel on Forge + Digital Ocean |

**UI framework decision:** Filament for both admin and venue portals. No React. Blade + Livewire for customer-facing pages. If a future feature requires rich client-side interactivity, Alpine.js or a React component can be added to a Livewire page without rearchitecting.

**Development:** Cursor + Remote SSH to Digital Ocean. No local Mac development unless explicitly requested.

**Webhook API:** Laravel exposes a POST endpoint for Elementor form submissions. Payload format and Elementor setup instructions documented in build. Endpoint validates, maps fields, and creates lead.

### 11.2 Database Tables (Draft)

- `ph_leads` – Lead records (including webhook payload reference)
- `ph_venues` – Venue data
- `ph_rooms` – Room records (including budget range / room hire cost)
- `ph_lead_matches` – Lead–venue matches
- `ph_lead_purchases` – Purchase records
- `ph_credits` – Credit ledger
- `ph_email_log` – Email tracking
- `ph_sms_log` – SMS tracking
- `ph_pricing_matrix` – Pricing config
- `ph_budget_ranges` – Configurable budget range options for form
- `ph_function_packs` – Function pack files (storage metadata, links)

### 11.3 Scheduled Tasks

| Task | Frequency |
|------|-----------|
| Lead distribution | Continuous |
| 24/48/72 hr discount/expiry checks | Hourly |
| Customer +8/36/72 hr emails | Every 15 min |
| Credit balance / auto top-up | Every 5 min |
| Daily reconciliation | 2am |
| Xero sync | 3am |

---

## 12. Non-Functional Requirements

### 12.1 Performance

- Lead matching + distribution: &lt; 60 seconds
- Lead purchase: &lt; 3 seconds
- Emails: &lt; 30 seconds
- SMS: &lt; 10 seconds
- Dashboard: &lt; 3 seconds

### 12.2 Scalability

- 500+ active venues
- 100 concurrent form submissions
- 10,000 leads/month
- 50,000 emails/month

### 12.3 Security

- SSL/TLS
- PCI DSS via Stripe (no card storage)
- Australian Privacy Act
- Strong passwords, rate limiting, CAPTCHA on forms

### 12.4 Availability

- 99.9% uptime
- Daily backups, 30-day retention
- 4-hour RTO

### 12.5 Coding Standards

- Max 300 lines per PHP/functional file (Blade/templates may exceed)
- Max 500 lines where a single file is justified
- Regular commits to GitHub
- Proactive refactoring
- Daily code-size checks (manual in production)
- Rules in git and Cursor config

### 12.6 Unit Testing

- Tests added as features are built
- Run after major changes
- Daily scheduled runs, fix failures

### 12.7 Documentation

- PRD, architecture, assumptions
- Sensible code comments
- Proactive updates

---

## 13. Success Metrics

| Metric | Target | Measurement |
|--------|--------|-------------|
| Lead fulfilment rate | &gt; 70% | Fulfilled / Total |
| Time to fulfilment | &lt; 4 hours | Distribution → fulfilment |
| Venue purchase rate | &gt; 15% | Purchases / Opportunities |
| Customer email engagement | &gt; 40% open rate | Opens / Delivered |
| Booking conversion | &gt; 20% | Bookings / Total leads |
| Payment success | &gt; 98% | Successful / Attempted top-ups |
| Venue retention | &gt; 85% | Active after 6 months |

---

## 14. Appendix

### 14.1 Glossary

| Term | Definition |
|------|------------|
| Lead | Customer enquiry submitted via Partyhelp |
| Lead Opportunity | Notification to venue about available lead |
| Lead Purchase | Venue pays credits for full customer details |
| Fulfilment | Required number of venues (3–5) have purchased |
| Credit | Prepaid balance for lead purchases |
| Auto Top-Up | Automatic credit purchase below threshold |
| Room Style | Bar, Function Room, Pub, Club, Semi-Outdoor |

### 14.2 Document History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0 | Feb 2026 | Business Assist | Initial creation |
| 2.0 | Feb 2026 | Business Assist | UX, venue portal, admin, credits, automation |
| 2.1 | Feb 2026 | Business Assist | Webhook integration, venue approval email, budget ranges, function packs, colour schemes, responsive design |
| 2.2 | Feb 2026 | Business Assist | Filament architecture decision, `<x-layout>` convention, no React |

---

## 15. Resolved Design Decisions

| Topic | Decision |
|-------|----------|
| Customer notification grouping | Not implemented initially; may introduce time-window grouping later |
| Melbourne suburbs | Use public source (e.g. ABS) |
| WordPress → Laravel integration | Elementor webhook POSTs form data to Laravel; build includes webhook endpoint and docs |
| Venue approval | "New venue for approval" email to admin with Review / Approve / Reject buttons |
| Budget ranges | Form passes configurable text values (e.g. 1500-3000); venues have room budget ranges; rules to refine later |
| Function packs | Binary files (PDF, PPTX); server-side storage; links in emails; hosted download page |
| Low-match alerts | Email + SMS to admin when &lt; 10 venues match |
| Form location | WordPress (Elementor) in first instance |
| Venue portal | Mobile-friendly |
| Admin portal | Desktop-primary |
| Colour schemes | Admin + Venue: light. End-customer (party planner) UI: dark (match partyhelp.com.au) |
| UI framework | Filament for admin + venue portals. No React. Blade + Livewire for customer pages |
| Blade convention | Use `<x-layout>` component tags, NOT `@include` statements |
