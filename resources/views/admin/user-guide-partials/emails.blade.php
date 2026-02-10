<section id="emails" class="scroll-mt-8">
    <h2 class="text-xl font-semibold border-b border-gray-200 dark:border-gray-700 pb-2">7. Email integration (SendGrid)</h2>
    <p>All outbound email is sent via the <strong>SendGrid API</strong> (the server does not use SMTP). Templates are stored in Laravel and synced to SendGrid as <strong>dynamic templates</strong> so you can edit copy in the admin and push changes without touching SendGrid’s UI.</p>

    <h3>7.1 Manage email templates</h3>
    <p>Go to <strong>Manage System Data → Manage Emails</strong> to list and edit templates. Each template has a key (e.g. form confirmation, lead opportunity, venue introduction), subject line, and <strong>content slots</strong> (placeholders that map to SendGrid dynamic template variables). After editing, use <strong>Sync to SendGrid</strong> to create or update the template in SendGrid; use <strong>Force sync</strong> if you need to overwrite without change detection.</p>

    <h3>7.2 Sending a test email</h3>
    <p>On the <strong>edit page</strong> of any email template you’ll see a <strong>Send test email</strong> action. Click it, enter an email address, and submit. The system sends a test message using that template with <strong>sample data</strong> (e.g. sample customer name, lead details, venue names). This helps you verify layout and content without creating real leads or purchases. Check the inbox (and spam) of the address you entered. If SendGrid is misconfigured or the template is invalid, you’ll see an error notification.</p>

    <h3>7.3 Artisan test commands</h3>
    <p>For quick tests from the command line:</p>
    <ul>
        <li><strong>Form confirmation:</strong> <code>php artisan email:test-form-confirmation --to=you@example.com</code> (optional: <code>--name=</code>, <code>--website=</code>).</li>
        <li><strong>Venue introduction:</strong> <code>php artisan email:test-venue-introduction --to=you@example.com</code> (optional: <code>--name=</code>, <code>--location=</code>).</li>
    </ul>
    <p>These send the corresponding Laravel mailable with sample data to the given address. Ensure <code>MAIL_MAILER=sendgrid</code> and <code>SENDGRID_API_KEY</code> are set in <code>.env</code> so the message goes through SendGrid.</p>

    <h3>7.4 Email logs</h3>
    <p>Use <strong>Manage System Data → Emails</strong> (email logs) to view a history of sent emails. Useful to confirm deliveries and debug issues (e.g. wrong template, missing variable).</p>
</section>
