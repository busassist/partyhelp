<section id="pricing-discounts" class="scroll-mt-8">
    <h2 class="text-xl font-semibold border-b border-gray-200 dark:border-gray-700 pb-2">6. Budget, pricing &amp; discounts</h2>
    <p>These settings directly affect the lead workflow: what the customer sees on the form, what venues pay per lead, and when prices drop over time.</p>

    <h3>6.1 Pricing matrix</h3>
    <p><strong>Manage System Pricing → Pricing Matrix</strong> defines the <strong>base price</strong> per lead by <strong>occasion type</strong> and <strong>guest count bracket</strong> (e.g. 10–29, 30–60). When a lead is distributed, the system looks up the price for that lead’s occasion and guest count and sets the lead’s <strong>base_price</strong> and <strong>current_price</strong>. If no matrix row matches, a default (e.g. $29.99) is used. The same guest brackets are exposed to the WordPress form as “guest brackets” so the form and pricing stay aligned.</p>
    <p><strong>Business rule:</strong> Base price is set once at distribution. Discounts (below) reduce <strong>current_price</strong> over time; venues always pay the <strong>current price</strong> at the time of purchase.</p>

    <h3>6.2 Budget ranges</h3>
    <p><strong>Manage System Pricing → Budget Ranges</strong> defines the budget options shown on the form (e.g. “$500–$1,000”, “$1,000–$2,500”). The customer selects one; it is stored on the lead as <strong>budget_range</strong>. In <strong>matching</strong>, the engine uses this to score venues: rooms whose hire cost range overlaps the lead’s budget get a higher score. Budget ranges do not change the dollar price the venue pays for the lead—they only affect matching and customer/venue expectations.</p>
    <p><strong>Business rule:</strong> Budget range is for matching and display only. Lead price comes from the Pricing Matrix and Discount Settings.</p>

    <h3>6.3 Discount settings</h3>
    <p><strong>Manage System Pricing → Discount Settings</strong> define <strong>time-based discounts</strong>: after a number of hours since distribution, apply a discount percentage to the lead’s price. For example: “After 24 hours, 10% off”; “After 48 hours, 20% off.” The scheduled job <strong>Process Lead Discounts</strong> runs periodically, finds distributed or partially fulfilled leads that have passed each rule’s <em>hours_elapsed</em>, and updates <strong>discount_percent</strong> and <strong>current_price</strong> (current_price = base_price × (1 − discount_percent/100)). Optionally, <strong>resend notification</strong> can re-notify matched venues that the lead is now cheaper.</p>
    <p><strong>Business rules:</strong></p>
    <ul>
        <li>Only one discount tier applies per lead (the highest applicable); when a later tier kicks in, it overwrites the previous.</li>
        <li>When a venue purchases, the <strong>amount_paid</strong> and <strong>discount_percent</strong> at that moment are stored on the Lead Purchase record for reporting.</li>
        <li>Discounts encourage faster purchase (early venues pay full or higher price) and improve fulfilment (later venues get a lower price).</li>
    </ul>
</section>
