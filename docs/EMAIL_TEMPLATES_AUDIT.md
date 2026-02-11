# Email Templates Audit

Last audit: after lead-opportunity token/label fixes and single discount template. Use this to ensure **token replacement** (no literal `${{price}}`, `{{occasion}}`, etc.) and **labels** (e.g. "Function Room" not `function_room`) are correct, and to see which templates have Mailables.

## Conventions

- **Token replacement:** At send time, Mailables must pass real values for any placeholder used in subjects or content_slots (e.g. `price`, `creditBalance`, `occasion`, `discountPercent`). Replace `{{...}}` / `${{...}}` in slot text before rendering (or pass pre-built strings like `ctaButtonLabel`).
- **Labels:** For lead/venue emails, use human-readable labels from config:
  - **Occasion:** `config('partyhelp.occasion_types.' . $lead->occasion_type, $lead->occasion_type)` (e.g. "Christmas Party" not `christmas_party`).
  - **Room styles:** Map keys via `config('partyhelp.room_styles.' . $key)` (e.g. "Function Room, Club" not `function_room, club`).
- **Guest count display:** Prefer `$lead->guest_count_display` so range values (e.g. "46-60") from the form are shown when applicable.

---

## Template status

| # | Key | Mailable | Trigger / Job | Token replacement | Labels | Notes |
|---|-----|----------|----------------|-------------------|--------|--------|
| 1 | venue_introduction | `VenueIntroductionEmail` | `SendCustomerVenueIntroEmail` (on purchase) | N/A (no price/balance) | N/A | Passes customerName, location, venues. View uses config defaults for slots. |
| 2 | form_confirmation | `FormConfirmationEmail` | `SendCustomerConfirmationEmail` (webhook) | `websiteUrl` in Blade fallback via `bladeViewData()`; SendGrid gets dynamic_template_data | N/A | Fixed: Blade now gets content_slots with `{{websiteUrl}}` replaced. |
| 3 | no_few_responses_prompt | — | Not implemented (scheduled +8h) | — | When implemented: pass `occasion` as label, `guestCount` (or `guest_count_display`) | Needs Mailable + Job. View expects customerName, occasion, guestCount, slots. |
| 4 | shortlist_check | — | Not implemented (scheduled +36h) | — | N/A | Needs Mailable + Job. View expects customerName, slots. |
| 5 | additional_services_lead_expiry | — | Not implemented (+72h) | — | N/A | Needs Mailable + Job. View expects customerName, slots. |
| 6 | lead_opportunity | `LeadOpportunityEmail` (discountPercent=0) | `SendLeadOpportunityNotification` | price, creditBalance, ctaButtonLabel, footerBalanceText passed; Blade/SendGrid use real values | occasion label, room_styles labels, guest_count_display | Done. |
| 7 | lead_opportunity_discount | `LeadOpportunityEmail` (discountPercent>0) | `ProcessLeadDiscounts` → `SendLeadOpportunityNotification` | Same as 6 + discountPercent; replacePlaceholders in slot text | Same as 6 | Done. |
| 8 | lead_no_longer_available | `LeadNoLongerAvailableEmail` | Not yet dispatched (on fulfil/expiry) | suburb, occasion (label), reason, dashboardUrl | occasion = label | Mailable ready. Wire Job when fulfil/expiry flow is implemented. |
| 9 | function_pack | `FunctionPackEmail` | Not yet dispatched (on lead purchase) | venueName, downloadUrl, expiryNote | N/A | Mailable ready. Wire Job when function pack delivery is implemented. |
| 10 | failed_topup_notification | `FailedTopupNotificationEmail` | Not yet dispatched (on failed top-up) | venueName, attemptedAmount, failureReason, updatePaymentUrl | N/A | Mailable ready. Wire Job when top-up failure handling is implemented. |
| 11 | invoice_receipt | `VenueReceiptEmail` | `StripeCheckoutService` (after payment) | Subject + content: documentType, invoiceNumber, amount, viewUrl from templateData() | N/A | Done. Blade and SendGrid get real values. |
| 12 | new_venue_for_approval | `VenueApprovalEmail` | `SendVenueApprovalEmail` | venueName, reviewUrl, approveUrl, rejectUrl passed | N/A | Done. Blade only (no SendGrid). |
| 13 | low_match_alert | `LowMatchAlertEmail` | `SendLowMatchAlertEmail` (from `LeadDistributionService::notifyAdminLowMatches`) | suburb, occasion (label), guestCount_display, matchCount, dashboardLeadUrl | occasion = label | Done. Uses config partyhelp.admin_email. |

---

## Checklist when adding a Mailable

1. **Subject:** If subject uses placeholders (e.g. `{{occasion}}`, `{{price}}`), replace them in `envelope()` or pass full subject with values.
2. **Content slots from EmailTemplate:** If loading from DB, run a `replacePlaceholders()`-style step for `{{price}}`, `{{creditBalance}}`, `{{occasion}}`, `{{discountPercent}}`, etc., so no literal tokens are sent.
3. **Labels:** For any lead/venue context, pass `occasion` as `config('partyhelp.occasion_types.' . $lead->occasion_type)` and room styles as comma-separated labels from `config('partyhelp.room_styles')`.
4. **Blade fallback:** When not using SendGrid, pass a `with` array so the view receives all variables (no raw `${{...}}` in output). Use defaults like `'Purchase This Lead - $' . ($price ?? '—')` for CTA/footer.

---

## Files reference

- **Mailables:** `app/Mail/` (FormConfirmationEmail, LeadOpportunityEmail, VenueApprovalEmail, VenueIntroductionEmail, VenueReceiptEmail).
- **Views:** `resources/views/emails/*.blade.php`.
- **Config labels:** `config/partyhelp.php` → `occasion_types`, `room_styles`.
- **Seeder (default content_slots):** `database/seeders/EmailTemplateSeeder.php`.
