<section id="workflow" class="scroll-mt-8">
    <h2 class="text-xl font-semibold border-b border-gray-200 dark:border-gray-700 pb-2">1. End-to-end lead workflow</h2>
    <p class="lead">This section describes the full journey from a customer submitting an enquiry to venues being notified and customers receiving venue options.</p>

    <h3>1.1 Customer submits the form</h3>
    <p>The customer fills in the party details form on the WordPress site (or any front-end that posts to the Laravel webhook). Fields include: name, email, phone, occasion type, preferred date, guest count, budget range, location (areas/suburbs), and room style preferences.</p>

    <h3>1.2 Webhook receives the submission</h3>
    <p>Laravel receives the submission at <code>POST /api/webhook/elementor-lead</code>. The payload is validated and mapped to lead fields. A new <strong>Lead</strong> record is created with status <strong>New</strong>.</p>

    <h3>1.3 Lead distribution</h3>
    <p>The system runs the <strong>matching engine</strong> to find up to 30 venues that best match the lead (location, capacity, room style, occasion type, and budget). For each matched venue:</p>
    <ul>
        <li>A <strong>Lead Match</strong> record is created (status: notified).</li>
        <li>A <strong>lead opportunity notification</strong> (email, and optionally SMS) is queued and sent to the venue.</li>
    </ul>
    <p>The lead’s <strong>base price</strong> is set from the <strong>Pricing Matrix</strong> (by occasion type and guest count). The lead status becomes <strong>Distributed</strong> and an expiry time is set (e.g. 72 hours). The customer receives a <strong>form confirmation email</strong> setting expectations on when they’ll hear from venues.</p>

    <h3>1.4 Venues see the opportunity</h3>
    <p>Venues log in at <strong>/venue</strong> and see the lead in <strong>Available Leads</strong>. They can view summary details and the current price. If they have sufficient credit, they can <strong>purchase</strong> the lead.</p>

    <h3>1.5 Lead purchase</h3>
    <p>When a venue purchases a lead:</p>
    <ul>
        <li>Their <strong>credit balance</strong> is debited by the lead’s <strong>current price</strong>.</li>
        <li>A <strong>Lead Purchase</strong> record is created (amount paid, discount % if any).</li>
        <li>The lead’s <strong>purchase count</strong> increases. If it reaches the <strong>purchase target</strong> (e.g. 3), the lead becomes <strong>Fulfilled</strong> and is no longer available to other venues.</li>
        <li>The <strong>customer</strong> is sent a <strong>Venue Introduction</strong> email with that venue’s details so they can make contact.</li>
    </ul>

    <h3>1.6 Discount escalation</h3>
    <p><strong>Discount settings</strong> define time-based price reductions (e.g. after 24 hours, apply 10% off). A scheduled job (<strong>Process Lead Discounts</strong>) applies these discounts to distributed/partially fulfilled leads that have passed the configured hours. The lead’s <strong>current price</strong> is reduced, encouraging later purchasers. Optionally, venues can be re-notified when a discount is applied.</p>

    <h3>1.7 Summary</h3>
    <p>In short: <strong>Form → Webhook → Lead created → Matching → Notify up to 30 venues → Venues purchase (credit debited) → Customer gets venue intro emails → Lead fulfilled when target purchases reached.</strong> Budget ranges and pricing/discounts influence the form options, initial price, and ongoing price reductions.</p>
</section>
