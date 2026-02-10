<section id="wordpress-plugin" class="scroll-mt-8">
    <h2 class="text-xl font-semibold border-b border-gray-200 dark:border-gray-700 pb-2">8. WordPress plugin</h2>
    <p>The <strong>Partyhelp Form</strong> WordPress plugin provides the customer-facing enquiry form and syncs its options from the Laravel app. It is installed on the WordPress site (e.g. partyhelp.com.au); Laravel exposes API endpoints for config and receives submissions via webhook.</p>

    <h3>8.1 What the plugin does</h3>
    <ul>
        <li>Renders the party details form (name, email, phone, occasion, date, guests, budget, location areas, room styles, notes) using the shortcode <code>[partyhelp-form]</code>.</li>
        <li>Fetches form options from Laravel: <strong>areas</strong>, <strong>occasion types</strong>, <strong>guest brackets</strong>, <strong>budget ranges</strong>, <strong>venue styles</strong> (with images).</li>
        <li>On submit, sends the form data to the Laravel <strong>webhook</strong> (<code>POST /api/webhook/elementor-lead</code>), which creates the lead and triggers distribution.</li>
        <li>Optionally redirects the customer to a thank-you URL after success.</li>
    </ul>

    <h3>8.2 Settings screen (WordPress)</h3>
    <p>In WordPress go to <strong>Settings → Partyhelp Form</strong>. Key settings:</p>
    <ul>
        <li><strong>API Base URL</strong> — Base URL for config (default: <code>https://get.partyhelp.com.au/api/partyhelp-form</code>). The plugin appends <code>/config</code>, <code>/areas</code>, etc.</li>
        <li><strong>Webhook URL</strong> — Where submissions are sent (default: <code>https://get.partyhelp.com.au/api/webhook/elementor-lead</code>). Must match the Laravel route.</li>
        <li><strong>Redirect URL (after success)</strong> — Optional. Full URL or path (e.g. <code>/thank-you</code>) to send the customer to after a successful submit.</li>
        <li><strong>Sync frequency (minutes)</strong> — How often the plugin refreshes config from the API (default 60). Also use <strong>Sync Now</strong> to refresh immediately after you change areas, occasion types, budget ranges, or venue styles in the admin.</li>
        <li><strong>Debug</strong> — Enable debug mode and set <strong>Debug API URL</strong> to send plugin debug payloads to Laravel (e.g. <code>https://get.partyhelp.com.au/api/partyhelp-form/debug-log</code>). View logs in <code>storage/logs/laravel.log</code>.</li>
    </ul>

    <h3>8.3 Form appearance (WordPress)</h3>
    <p>The same settings page includes <strong>Form appearance</strong>: form background colour, label colour, text colour, font families, field border radius, and border colour. These map to CSS variables used by the form so you can match your site’s branding without editing plugin code.</p>

    <h3>8.4 Controls summary</h3>
    <p>You control <strong>what options appear</strong> on the form by managing data in the admin: Areas, Occasion Types, Budget Ranges, Venue Styles, and (indirectly) guest brackets via the Pricing Matrix. After changing any of these, run <strong>Sync Now</strong> in WordPress so the form reflects the latest data. The plugin does not store its own copy of options long-term; it caches the last successful sync and refreshes on the configured interval or when you click Sync Now.</p>
</section>
