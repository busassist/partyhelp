<div class="space-y-4">
    <p class="text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('admin.additional-service-submissions.csv') }}"
           target="_blank"
           rel="noopener"
           class="inline-flex items-center gap-1 rounded-md px-3 py-2 text-sm font-medium"
           style="background-color:#7c3aed;color:#fff;">
            Download all as CSV
        </a>
    </p>
    @if ($submissions->isEmpty())
        <p class="text-sm text-gray-500 dark:text-gray-400">No received leads yet.</p>
    @else
        <div class="max-h-[60vh] overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0">
                    <tr>
                        <th scope="col" class="px-3 py-2 text-left font-medium text-gray-900 dark:text-white">Email</th>
                        <th scope="col" class="px-3 py-2 text-left font-medium text-gray-900 dark:text-white">Submitted</th>
                        <th scope="col" class="px-3 py-2 text-left font-medium text-gray-900 dark:text-white">Services selected</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900/50">
                    @foreach ($submissions as $s)
                        <tr>
                            <td class="px-3 py-2 text-gray-900 dark:text-gray-100">{{ $s->respondent_email }}</td>
                            <td class="px-3 py-2 text-gray-600 dark:text-gray-400 tabular-nums">{{ $s->submitted_at?->format('Y-m-d H:i') }}</td>
                            <td class="px-3 py-2 text-gray-600 dark:text-gray-300">{{ implode(', ', array_values($s->selected_service_names)) ?: '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
