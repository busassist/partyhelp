<section id="emails" class="scroll-mt-8">
    <h2 class="text-xl font-semibold border-b border-gray-200 dark:border-gray-700 pb-2">7. Email integration (Mailgun)</h2>
    <p>All outbound email is sent via the <strong>Mailgun API</strong> (the server does not use SMTP). Templates are managed entirely on this server: content is stored in Laravel and rendered as Blade views. Mailgun is used only as the sending infrastructure; no templates are uploaded to Mailgun.</p>

    <h3>7.1 Manage email templates</h3>
    <p>Go to <strong>Manage System Data → Manage Emails</strong> to list and edit templates. Each template has a key (e.g. form confirmation, lead opportunity, venue introduction), subject line, and <strong>content slots</strong> (editable snippets used in the Blade views). Edit the slots and save; changes take effect immediately for the next email sent.</p>

    <h3>7.2 Sending a test email</h3>
    <p>On the <strong>edit page</strong> of any email template you’ll see a <strong>Send test email</strong> action. Click it, enter an email address, and submit. The system sends a test message using that template with <strong>sample data</strong> via Mailgun. Not every template key supports test send (e.g. some require a real lead or venue); for those you’ll see a message that test send is not available. Check the inbox (and spam) of the address you entered. If Mailgun is misconfigured you’ll see an error notification.</p>

    <h3>7.3 Artisan test commands</h3>
    <p>For quick tests from the command line:</p>
    <ul>
        <li><strong>Form confirmation:</strong> <code>php artisan email:test-form-confirmation --to=you@example.com</code> (optional: <code>--name=</code>, <code>--website=</code>).</li>
        <li><strong>Venue introduction:</strong> <code>php artisan email:test-venue-introduction --to=you@example.com</code> (optional: <code>--name=</code>, <code>--location=</code>).</li>
    </ul>
    <p>These send the corresponding Laravel mailable with sample data to the given address. Ensure <code>MAIL_MAILER=mailgun</code> and <code>MAILGUN_DOMAIN</code> / <code>MAILGUN_SECRET</code> are set in <code>.env</code> so the message goes through Mailgun.</p>

    <h3>7.4 Email logs</h3>
    <p>Use <strong>Manage System Data → Emails</strong> (email logs) to view a history of sent emails. Useful to confirm deliveries and debug issues.</p>
</section>
