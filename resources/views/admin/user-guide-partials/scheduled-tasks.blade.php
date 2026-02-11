<section id="scheduled-tasks" class="scroll-mt-8">
    <h2 class="text-xl font-semibold border-b border-gray-200 dark:border-gray-700 pb-2">10. System scheduled tasks</h2>
    <p>The system runs a number of scheduled tasks in the background. These run automatically at set intervals; you do not need to trigger them manually. The timing and behaviour of some tasks are driven by configuration you set in the admin (for example, discount settings).</p>

    <h3>Process Lead Discounts</h3>
    <p><strong>Purpose:</strong> Applies time-based price discounts to leads. When a lead has been distributed to venues and a configured time has passed (e.g. 24 hours, or 0 hours 2 minutes for testing), the lead’s price is reduced by the discount percentage defined in <strong>Manage System Pricing → Discount Settings</strong>. This encourages later purchasers and improves lead fulfilment.</p>
    <p><strong>Runs:</strong> Every hour. Each run checks all active discount rules and updates any leads that have passed the “hours and minutes after distribution” threshold.</p>
    <p><strong>Configuration:</strong> The exact timing (hours and minutes after distribution) and discount percentage for each tier are set on the Discount Settings edit screen. Any changes you save there are used on the next run of this task.</p>

    <h3>Expire Leads</h3>
    <p><strong>Purpose:</strong> Marks leads as expired when their closing time has passed. Leads that are still “distributed” or “partially fulfilled” but past their <strong>expires_at</strong> time are updated to “expired” so they no longer appear as available to venues.</p>
    <p><strong>Runs:</strong> Every hour.</p>

    <h3>Process Auto Top-Ups</h3>
    <p><strong>Purpose:</strong> Triggers automatic credit top-ups for venues that have enabled auto top-up and have a payment method on file. When a venue’s credit balance falls below their configured threshold, the system attempts to charge their card for the configured top-up amount so they can continue receiving lead opportunities.</p>
    <p><strong>Runs:</strong> Every five minutes. Venue-level thresholds and amounts are configured in the venue portal under Manage Billing &amp; Credits → Payment Methods.</p>
</section>
