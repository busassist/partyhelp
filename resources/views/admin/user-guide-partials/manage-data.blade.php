<section id="manage-data" class="scroll-mt-8">
    <h2 class="text-xl font-semibold border-b border-gray-200 dark:border-gray-700 pb-2">5. Manage system data</h2>
    <p>Under <strong>Manage System Data</strong> you maintain reference data used across the platform and in the WordPress form.</p>

    <h3>5.1 Manage Locations</h3>
    <p>See section <strong>4.3 Venues &amp; rooms</strong> for Areas and Postcodes. These drive form location options and venue–lead location matching.</p>

    <h3>5.2 Postcodes</h3>
    <p>Standalone <strong>Postcodes</strong> resource to manage suburb/postcode records. Used by areas and by the form config API.</p>

    <h3>5.3 Occasion types</h3>
    <p><strong>Occasion Types</strong> define the event types (e.g. 21st Birthday, Wedding Reception, Corporate Function). They appear in the form dropdown and in the <strong>Pricing Matrix</strong> (price per occasion + guest bracket). Matching uses occasion type to score venues that suit the lead’s occasion.</p>

    <h3>5.4 Room features</h3>
    <p><strong>Room Features</strong> are optional attributes you can attach to rooms (e.g. AV, outdoor area). Used for display and filtering; ensure they stay in sync with how rooms are described.</p>

    <h3>5.5 Venue styles</h3>
    <p><strong>Venue Styles</strong> (e.g. Bar, Function Room, Pub) are shown on the form as selectable options (with images if configured). The form config API exposes these; the plugin syncs them so the form always shows current styles. Matching uses room style to score leads. Keep labels and keys consistent with the form and matching logic.</p>

    <h3>5.6 Emails (templates &amp; logs)</h3>
    <p><strong>Manage Emails</strong> — Edit and manage <strong>email templates</strong> used for SendGrid dynamic templates. You can edit content slots, send test emails, and sync templates to SendGrid. See section <strong>7. Email integration</strong> for details.</p>
    <p><strong>Emails</strong> (logs) — View <strong>email logs</strong> for sent messages (e.g. lead opportunity, venue intro, form confirmation). Useful for debugging and compliance.</p>
</section>
