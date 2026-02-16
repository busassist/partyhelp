# Twilio WhatsApp – Lead opportunity Accept / Ignore

When a venue is notified about a new lead (email), we can also send a WhatsApp message with **Accept** and **Ignore** buttons. The venue taps a button; if they tap **Accept**, we reply with the purchase link.

## 1. Create the Content Template in Twilio

1. In [Twilio Console](https://console.twilio.com) go to **Messaging** → **Content Template Builder** (or **Try it out** → **Content**).
2. Create a new template with:
   - **Language**: English
   - **Content type**: add **twilio/quick-reply**
   - **Body** (max 1,024 chars), e.g.:

     ```
     Accept and pay for this lead. **Important: your Partyhelp credits balance will be automatically deducted.** Ignore this message if you do not want to pay for this lead.
     ```

   - **Actions** (quick reply buttons):
     - Button 1: **Title** `Accept`, **ID** `{{1}}`
     - Button 2: **Title** `Ignore`, **ID** `{{2}}`
   - **Variables**: add sample values for approval, e.g. `1` → `accept_sample`, `2` → `ignore`.
3. Submit for WhatsApp approval if required (out-of-session templates need approval).
4. Copy the **Content SID** (starts with `HX...`).

## 2. Configure the app

- In `.env` set:
  - `TWILIO_LEAD_OPPORTUNITY_CONTENT_SID=HXxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx`
- In **Admin** → **Manage Emails** → edit the **Lead opportunity** (and optionally **Lead opportunity discount**) template and check **Send via WhatsApp (in addition to email)**.

## 3. Webhook for button replies

When the venue taps **Accept** or **Ignore**, Twilio sends the reply to our webhook. Configure your Twilio WhatsApp sender (Sandbox or number):

- **Webhook URL**: `https://get.partyhelp.com.au/webhook/twilio/whatsapp`
- Method: **POST**

The app will:
- On **Accept**: reply with the signed purchase link for that lead/venue.
- On **Ignore**: reply with “No problem – you won’t be charged for this lead.”

## 4. Venue phone number

Venues must have **Contact phone** set (e.g. `0412345678`). It is normalized to E.164 (`+61412345678`) for WhatsApp. Australian numbers only; other regions may need extending `TwilioWhatsAppService::normalizePhoneToE164()`.
