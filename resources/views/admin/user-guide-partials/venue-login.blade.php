<section id="venue-login" class="scroll-mt-8">
    <h2 class="text-xl font-semibold border-b border-gray-200 dark:border-gray-700 pb-2">9. Venue login (/venue)</h2>
    <p>Venues log in at <strong>/venue</strong> to a separate Filament panel (the “venue portal”). They do not see the admin panel; they only see their own data and the leads they are allowed to act on.</p>

    <h3>9.1 What’s included</h3>
    <ul>
        <li><strong>Dashboard</strong> — Overview widgets (e.g. credit balance, recent activity, available leads count).</li>
        <li><strong>My Rooms</strong> — The venue’s rooms: view, add, and edit room details (name, capacity, style, hire cost range, features, images, active flag). Venues can keep their room info up to date so matching and customer-facing info are accurate.</li>
        <li><strong>Available Leads</strong> — Leads that have been matched to this venue and are still available (status distributed or partially fulfilled, not expired). Each row shows summary info and the <strong>current price</strong>. The venue can <strong>Purchase Lead</strong> to debit their credit and receive the customer’s contact details.</li>
        <li><strong>Purchased Leads</strong> — Leads the venue has already purchased. Full contact details and lead details are visible here so the venue can follow up with the customer.</li>
    </ul>

    <h3>9.2 What venues can control</h3>
    <p>Venues can:</p>
    <ul>
        <li>Update their <strong>profile</strong> (name, contact details, etc., if exposed in the venue panel).</li>
        <li>Manage <strong>My Rooms</strong>: add rooms, set capacity and style, hire costs, features, and photos. This directly affects whether they match leads and how they appear in customer venue-intro emails.</li>
        <li>View <strong>Available Leads</strong> and <strong>purchase</strong> leads (subject to credit balance and lead availability).</li>
        <li>View <strong>Purchased Leads</strong> and use the customer details to make contact.</li>
    </ul>
    <p>Venues cannot:</p>
    <ul>
        <li>Access the admin panel, other venues’ data, or system settings.</li>
        <li>Change lead pricing, discount rules, or global config.</li>
        <li>See leads they were not matched to.</li>
    </ul>
    <p>Admin tasks (creating venues, adding credit, managing areas, pricing, email templates, etc.) are done only in the <strong>/admin</strong> panel.</p>
</section>
