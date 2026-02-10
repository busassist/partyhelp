# SendGrid Venue Introduction Template

The Venue Introduction email uses a SendGrid dynamic template. Create the template in SendGrid and set its ID in `.env`:

```
SENDGRID_TEMPLATE_VENUE_INTRODUCTION=d-xxxxxxxxxxxxxxxxxxxxxxxx
```

## Dynamic template data

The following variables are passed as `dynamic_template_data`:

| Variable | Type | Description |
|----------|------|-------------|
| `logoUrl` | string | Full URL to white logo (ph-logo-white.png) for dark theme |
| `customerName` | string | Customer's first name |
| `location` | string | Location string (e.g. "INNER NORTH - Carlton, Collingwood, Fitzroy, Brunswick") |
| `venues` | array | Array of venue objects (see below) |
| `viewInBrowserUrl` | string \| null | Link for "Can't read this email? View in the browser" |
| `unsubscribeUrl` | string \| null | Unsubscribe link |
| `appUrl` | string | App base URL |
| `footer_tagline` | string | Footer tagline from config |
| `support_email` | string | Support contact (e.g. venues@partyhelp.com.au) |
| `sign_off_name` | string | Sign-off name (e.g. Johnny) |
| `sign_off_title` | string | Sign-off title (e.g. Manager, Party Venues) |
| `business_address` | string | Business address line |

### Venue object structure

Each item in `venues`:

| Key | Type | Description |
|-----|------|-------------|
| `venue_name` | string | Venue display name |
| `venue_area` | string | Area/location (e.g. "Carlton/CBD") |
| `contact_name` | string | Contact person or "Functions Coordinator" |
| `contact_phone` | string | Phone number |
| `email` | string | Venue email address |
| `website` | string | Venue website URL |
| `room_hire` | string | Room hire cost (e.g. "$0" or "Yes, please speak with...") |
| `minimum_spend` | string | Minimum spend (e.g. "$1,500" or "Yes, please speak with...") |
| `rooms` | array | Array of matching room objects (see below) |

### Room object structure

Rooms matching the lead profile (party size etc.):

| Key | Type | Description |
|-----|------|-------------|
| `room_name` | string | Room display name |
| `description` | string | Room description |
| `image_url` | string | Full-width room photo URL (use 4:3 aspect ratio in template) |
| `capacity_min` | int | Minimum capacity (optional) |
| `capacity_max` | int | Maximum capacity (optional) |

### Layout: room photos

- **Desktop:** 2 room photos per row
- **Mobile:** 1 room photo per row (stacked)

Use a 2-column table with `@media (max-width: 600px)` to stack: set cells to `display: block; width: 100%` on mobile.

### Handlebars example

```
Can't read this email? <a href="{{viewInBrowserUrl}}">View in the browser</a>

Hi {{customerName}},

Thank you for your party enquiry. We can see you expressed interest in: {{location}}.

{{#each venues}}
  <h2>{{venue_name}} - {{venue_area}}</h2>
  <p>Contact: {{contact_name}} - {{contact_phone}}</p>
  <p>Email: {{email}} | Website: {{website}}</p>
  <p>Room Hire: {{room_hire}} | Minimum Spend: {{minimum_spend}}</p>

  {{#each rooms}}
    <img src="{{image_url}}" alt="{{room_name}}" style="width:100%;aspect-ratio:4/3;object-fit:cover;">
    <h3>{{room_name}}</h3>
    <p>{{description}} · Capacity: {{capacity_min}}-{{capacity_max}} guests</p>
  {{/each}}
{{/each}}

Regards, {{sign_off_name}}, {{sign_off_title}}
{{business_address}}
<a href="{{unsubscribeUrl}}">Unsubscribe</a>
```

## Email content flow

1. "Can't read this email? View in the browser"
2. Header: partyhelp branding
3. "Some venue recommendations tailored for you..."
4. Intro: Hi {customerName}, thank you for your enquiry, location {location}
5. For each venue: details block + rooms section
6. Closing: Contact support, Regards Johnny, business address
7. Unsubscribe link

Design reference: `docs/email-sample-template.html`
