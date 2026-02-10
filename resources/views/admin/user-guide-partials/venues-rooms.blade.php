<section id="venues-rooms" class="scroll-mt-8">
    <h2 class="text-xl font-semibold border-b border-gray-200 dark:border-gray-700 pb-2">4. Venues &amp; rooms</h2>

    <h3>4.1 Venues</h3>
    <p><strong>Venues</strong> in the sidebar lists all venue records. Each venue has: business name, contact details, suburb, area (linked to areas/postcodes), status (e.g. active), credit balance, occasion tags, room style tags, and optional media. You can create and edit venues, and manage their <strong>rooms</strong> via the relation manager on the venue edit page. Venues must be <strong>active</strong> and have <strong>credit</strong> to be included in lead matching.</p>

    <h3>4.2 Rooms (admin)</h3>
    <p><strong>Rooms</strong> can be managed from the Venues section (per-venue) or via the top-level <strong>Rooms</strong> resource. Each room has: name, min/max capacity, style, hire cost min/max, features, active flag, and optional images. Room capacity and style feed into the <strong>matching algorithm</strong> (capacity and room style scoring). Budget range on the lead is matched against room hire cost ranges when scoring.</p>

    <h3>4.3 Manage Locations</h3>
    <p><strong>Manage System Data → Manage Locations</strong> opens a page with two tabs:</p>
    <ul>
        <li><strong>Locations (Areas)</strong> — Define areas (e.g. CBD, Inner North) and link them to postcodes/suburbs. Venues are assigned to an area; the form and matching use this for location options and scoring.</li>
        <li><strong>Postcodes</strong> — List and edit postcodes and suburbs. Areas are built from these; ensure suburbs are consistent with the form and venue data.</li>
    </ul>
    <p>Keeping areas and postcodes up to date ensures the WordPress form shows the right location options and that lead-to-venue matching by location works correctly.</p>
</section>
