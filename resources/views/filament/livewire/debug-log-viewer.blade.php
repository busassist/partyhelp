<div class="space-y-2">
    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Recent debug log (last 50)</h4>
    @if ($entries->isEmpty())
        <p class="text-sm text-gray-500 dark:text-gray-400">No log entries yet. Enable debug logging and process leads to see activity.</p>
    @else
        <div class="rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800/50 overflow-hidden">
            <ul class="divide-y divide-gray-200 dark:divide-gray-600 max-h-[400px] overflow-y-auto">
                @foreach ($entries as $entry)
                    <li class="px-3 py-2 text-sm">
                        <span class="text-gray-500 dark:text-gray-400 font-mono">{{ $entry->created_at->format('Y-m-d H:i:s') }}</span>
                        @switch($entry->type)
                            @case('lead_received')
                                <span class="text-gray-700 dark:text-gray-300">
                                    Lead received: {{ $entry->payload['name'] ?? '—' }}, {{ $entry->payload['email'] ?? '—' }}, Location: {{ $entry->payload['location'] ?? '—' }}
                                </span>
                                @break
                            @case('venues_matched')
                                <span class="text-gray-700 dark:text-gray-300">
                                    Venues matched: {{ $entry->payload['count'] ?? 0 }} — {{ is_array($entry->payload['venue_names'] ?? null) ? implode(', ', $entry->payload['venue_names']) : '—' }}
                                </span>
                                @break
                            @case('email_sent')
                                <span class="text-gray-700 dark:text-gray-300">
                                    Email sent: {{ $entry->payload['email'] ?? '—' }}{!! isset($entry->payload['venue']) ? ' (venue: ' . e($entry->payload['venue']) . ')' : '' !!}
                                </span>
                                @break
                            @default
                                <span class="text-gray-700 dark:text-gray-300">{{ $entry->type }}: {{ json_encode($entry->payload) }}</span>
                        @endswitch
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
