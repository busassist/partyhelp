<x-layouts.customer title="Already Processed">
    <div class="content-card-centered">
        <div class="text-6xl mb-4">ℹ️</div>
        <h1 class="page-title-centered">
            Already Processed
        </h1>
        <p class="muted-text">
            <strong>{{ $venue->business_name }}</strong> has already been processed.
            Current status: <strong>{{ ucfirst($venue->status) }}</strong>
        </p>
    </div>
</x-layouts.customer>
