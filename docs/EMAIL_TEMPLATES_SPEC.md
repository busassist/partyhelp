# Email Templates Spec (14 templates)

PRD Section 6.4. Templates 1–2 are built; 3–14 are scaffolded here with structure, subject, slots, and dynamic data.

**Conventions:** Admin-editable text is stored in `content_slots` per template. System data is passed at send time as `dynamic_template_data`. Subject lines may include Handlebars e.g. `{{occasion}}`, `{{suburb}}`, `{{price}}`.

---

## Customer (Party Planner) – 5 emails

| # | Key | Trigger | Timing |
|---|-----|---------|--------|
| 1 | form_confirmation | Form submitted | Immediate ✓ built |
| 2 | venue_introduction | Each venue purchase | Per purchase ✓ built |
| 3 | no_few_responses_prompt | Few/no venue purchases | +8 hours |
| 4 | shortlist_check | Mid-journey | +36 hours |
| 5 | additional_services_lead_expiry | End of lead window | +72 hours |

### 3. no_few_responses_prompt
- **Subject:** Admin-editable (e.g. "Still looking for your perfect venue? We're on it")
- **Slots:** header_text, intro_text, reassurance_text, cta_text, closing_text
- **Dynamic:** customerName, location, occasion, guestCount, viewInBrowserUrl, unsubscribeUrl
- **Structure:** Logo, header, Hi {{customerName}}, intro + reassurance, CTA (e.g. contact us), sign-off.

### 4. shortlist_check
- **Subject:** Admin-editable (e.g. "How's your shortlist going?")
- **Slots:** header_text, intro_text, tips_text, cta_text, closing_text
- **Dynamic:** customerName, location, viewInBrowserUrl, unsubscribeUrl
- **Structure:** Logo, header, intro, bullet tips, CTA, sign-off.

### 5. additional_services_lead_expiry
- **Subject:** Admin-editable (e.g. "Your lead window has closed – here's what's next")
- **Slots:** header_text, expiry_intro_text, additional_services_text, cta_text, closing_text
- **Dynamic:** customerName, viewInBrowserUrl, unsubscribeUrl
- **Structure:** Logo, header, expiry message, additional services (e.g. more venues, booking help), CTA, sign-off.

---

## Venue – 7 emails

| # | Key | Trigger | Timing |
|---|-----|---------|--------|
| 6 | lead_opportunity | Lead distributed | Immediate |
| 7 | lead_opportunity_10pct | Discount escalation | +24 hours |
| 8 | lead_opportunity_20pct | Discount escalation | +48 hours |
| 9 | lead_no_longer_available | Fulfilled or expired | When threshold/72h |
| 10 | function_pack | Venue purchases lead | Immediate |
| 11 | failed_topup_notification | Auto top-up fails | Immediate |
| 12 | invoice_receipt | Payment or top-up | After successful payment |

### 6. lead_opportunity (PRD 6.1)
- **Subject:** `New {{occasion}} Lead - {{suburb}} - {{guestCount}} guests - ${{price}}`
- **Slots:** intro_text, cta_button_label, footer_balance_text, topup_link_text
- **Dynamic:** occasion, suburb, guestCount, preferredDate, roomStyles, price, purchaseUrl, creditBalance, topUpUrl, viewInBrowserUrl
- **Structure:** Logo, lead summary (occasion, guests, date, suburb, room style, price), CTA "Purchase This Lead - ${{price}}", footer credit balance + top-up link.

### 7. lead_opportunity_10pct
- **Subject:** Same as 6 with discount note, e.g. `New {{occasion}} Lead - {{suburb}} - 10% off - ${{price}}`
- **Slots:** Same as 6 + discount_intro_text (e.g. "This lead is now 10% off for the next 24 hours.")
- **Dynamic:** As 6 + discountPercent (10), newPrice
- **Structure:** As 6 with discount banner/intro.

### 8. lead_opportunity_20pct
- **Subject:** Same pattern, 20% off.
- **Slots:** Same as 7, discount_intro_text for 20%.
- **Dynamic:** As 7, discountPercent (20).
- **Structure:** As 7.

### 9. lead_no_longer_available
- **Subject:** `Lead no longer available - {{suburb}}`
- **Slots:** header_text, body_text, cta_text
- **Dynamic:** suburb, occasion, reason (fulfilled|expired), dashboardUrl, viewInBrowserUrl
- **Structure:** Short message: lead fulfilled or expired, CTA to view other leads.

### 10. function_pack
- **Subject:** Admin-editable (e.g. "Your function pack is ready to download")
- **Slots:** header_text, intro_text, download_button_label, closing_text
- **Dynamic:** venueName, customerName (or lead ref), downloadUrl, expiryNote, viewInBrowserUrl
- **Structure:** Logo, header, intro, prominent download link/button, closing.

### 11. failed_topup_notification
- **Subject:** Admin-editable (e.g. "Your Partyhelp credit top-up could not be processed")
- **Slots:** header_text, intro_text, cta_button_label, closing_text
- **Dynamic:** venueName, attemptedAmount, failureReason, updatePaymentUrl, viewInBrowserUrl
- **Structure:** Logo, header, intro, reason, CTA to update payment method.

### 12. invoice_receipt
- **Subject:** `Your Partyhelp {{documentType}} #{{invoiceNumber}}`
- **Slots:** header_text, intro_text, view_statement_label, closing_text
- **Dynamic:** venueName, documentType (Invoice|Receipt), invoiceNumber, amount, description, viewUrl, viewInBrowserUrl
- **Structure:** Logo, header, amount + description, link to view/download PDF.

---

## Admin – 2 emails

| # | Key | Trigger | Timing |
|---|-----|---------|--------|
| 13 | new_venue_for_approval | Venue registers | Immediate |
| 14 | low_match_alert | <10 venues match | Immediate |

### 13. new_venue_for_approval (PRD 8.1)
- **Subject:** `New venue pending approval: {{venueName}}`
- **Slots:** header_text, intro_text, review_button_label, approve_button_label, reject_button_label
- **Dynamic:** venueName, businessName, contactName, email, phone, reviewUrl, approveUrl, rejectUrl, viewInBrowserUrl
- **Structure:** Logo, header, venue summary, three CTAs: Review vendor, Approve, Reject.

### 14. low_match_alert (PRD 9.1)
- **Subject:** `Low-match alert: {{matchCount}} venues for lead in {{suburb}}`
- **Slots:** header_text, intro_text, view_lead_label
- **Dynamic:** suburb, occasion, guestCount, matchCount, leadId, dashboardLeadUrl, viewInBrowserUrl
- **Structure:** Logo, header, lead summary + match count, CTA to view lead in admin.
