# SendGrid Form Confirmation Template

The Form Confirmation email uses a SendGrid dynamic template. Create the template in SendGrid and set its ID in `.env`:

```
SENDGRID_TEMPLATE_FORM_CONFIRMATION=d-xxxxxxxxxxxxxxxxxxxxxxxx
```

## Dynamic template data

The following variables are passed as `dynamic_template_data`:

| Variable | Type | Description |
|----------|------|-------------|
| `logoUrl` | string | Full URL to white logo (ph-logo-white.png) for dark theme |
| `customerName` | string | Customer's first name |
| `websiteUrl` | string | Website where form was submitted (e.g. "www.EngagementParty.com.au") |
| `viewInBrowserUrl` | string \| null | Link for "Can't read this email? View in the browser" |
| `unsubscribeUrl` | string \| null | Unsubscribe link |
| `appUrl` | string | App base URL |
| `support_email` | string | Support contact (e.g. venues@partyhelp.com.au) |
| `sign_off_name` | string | Sign-off name (e.g. Johnny) |
| `sign_off_title` | string | Sign-off title (e.g. Manager, Party Venues) |
| `business_address` | string | Business address line |
| `ps_message` | string | PS paragraph (Gold Class draw, drink card incentive) |

## Email content flow

1. "Can't read this email? View in the browser"
2. Header: partyhelp branding + "Your tailored list of party venues is on the way!"
3. Hi {customerName}, thank you for leaving your party details with {websiteUrl}
4. **What happens now?** – bullet points
5. **What do I need to do?** – bullet points
6. Closing: contact support, Regards {sign_off_name}, {sign_off_title}
7. PS: {ps_message}
8. Business address
9. Unsubscribe link

## Handlebars example

```
Can't read this email? <a href="{{viewInBrowserUrl}}">View in the browser</a>

Your tailored list of party venues is on the way!

Hi {{customerName}},

Thank you for leaving your party details with our website {{websiteUrl}}. We will email you a tailored list of venues soon.

WHAT HAPPENS NOW?
- One of our customer service team members is analysing your party requirements...
- You will then receive an email with the tailored list of party venues

WHAT DO I NEED TO DO?
- Go through the list of venues...
- Speak with the functions managers...
- Visit with the venues and book your perfect party venue!

Contact me at {{support_email}}.
Regards, {{sign_off_name}}, {{sign_off_title}}
PS: {{ps_message}}
{{business_address}}
<a href="{{unsubscribeUrl}}">Unsubscribe</a>
```

Design reference: `docs/email-sample-template.html`
