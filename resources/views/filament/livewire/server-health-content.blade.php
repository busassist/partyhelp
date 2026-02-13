<div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div class="flex flex-col gap-6 p-6 sm:p-8">
        {{-- CPU --}}
        <section class="rounded-lg border border-gray-200 dark:border-gray-600/50 bg-gray-50/50 dark:bg-gray-800/30 px-5 py-5">
            <h3 class="text-sm font-semibold leading-6 text-gray-950 dark:text-white mb-4">CPU (load average)</h3>
            @if ($cpu['available'])
                <div class="flex flex-wrap gap-6 text-sm text-gray-700 dark:text-gray-300">
                    <span><span class="text-gray-500 dark:text-gray-400">1 min:</span> <strong class="font-semibold">{{ $cpu['load_1'] ?? '—' }}</strong></span>
                    <span><span class="text-gray-500 dark:text-gray-400">5 min:</span> <strong class="font-semibold">{{ $cpu['load_5'] ?? '—' }}</strong></span>
                    <span><span class="text-gray-500 dark:text-gray-400">15 min:</span> <strong class="font-semibold">{{ $cpu['load_15'] ?? '—' }}</strong></span>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">Not available on this system.</p>
            @endif
        </section>

        {{-- Disk --}}
        <section class="rounded-lg border border-gray-200 dark:border-gray-600/50 bg-gray-50/50 dark:bg-gray-800/30 px-5 py-5">
            <h3 class="text-sm font-semibold leading-6 text-gray-950 dark:text-white mb-4">Disk (application path)</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 font-mono break-all">{{ $disk['path'] }}</p>
            <div class="flex flex-wrap gap-6 text-sm text-gray-700 dark:text-gray-300 mb-4">
                <span><span class="text-gray-500 dark:text-gray-400">Used:</span> <strong class="font-semibold">{{ $disk['used_human'] }}</strong> ({{ $disk['used_percent'] }}%)</span>
                <span><span class="text-gray-500 dark:text-gray-400">Free:</span> <strong class="font-semibold">{{ $disk['free_human'] }}</strong></span>
                <span><span class="text-gray-500 dark:text-gray-400">Total:</span> <strong class="font-semibold">{{ $disk['total_human'] }}</strong></span>
            </div>
            <div class="h-2.5 w-full rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                <div class="h-full rounded-full {{ $disk['used_percent'] >= 90 ? 'bg-red-500' : ($disk['used_percent'] >= 75 ? 'bg-amber-500' : 'bg-blue-500') }}" style="width: {{ min($disk['used_percent'], 100) }}%"></div>
            </div>
        </section>

        {{-- Storage (Spaces / S3) --}}
        <section class="rounded-lg border border-gray-200 dark:border-gray-600/50 bg-gray-50/50 dark:bg-gray-800/30 px-5 py-5">
            <h3 class="text-sm font-semibold leading-6 text-gray-950 dark:text-white mb-4">Storage (Spaces / S3) — from Media records</h3>
            <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                Bucket consumption: <strong class="font-semibold">{{ $storage['total_human'] }}</strong> ({{ $storage['count'] }} file(s))
                @if (count($storage['by_venue']) > 0)
                    — <a href="#storage-by-venue" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">View consumption by venue</a>
                @endif
            </p>
            @if (count($storage['by_venue']) > 0)
                <div id="storage-by-venue" class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-600 -mx-px scroll-mt-4">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600 text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-800/50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300">Venue / Email</th>
                                <th scope="col" class="px-4 py-3 text-right font-medium text-gray-700 dark:text-gray-300 w-24">Size</th>
                                <th scope="col" class="px-4 py-3 text-right font-medium text-gray-700 dark:text-gray-300 w-20">Files</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600 bg-white dark:bg-gray-900/50">
                            @foreach ($storage['by_venue'] as $row)
                                <tr>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                        <div class="font-medium">{{ $row['business_name'] }}</div>
                                        <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ $row['email'] }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-right tabular-nums text-gray-700 dark:text-gray-300">{{ $row['human'] }}</td>
                                    <td class="px-4 py-3 text-right tabular-nums text-gray-700 dark:text-gray-300">{{ $row['count'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No media files recorded.</p>
            @endif
        </section>

        {{-- API connections --}}
        <section class="rounded-lg border border-gray-200 dark:border-gray-600/50 bg-gray-50/50 dark:bg-gray-800/30 px-5 py-5" x-data="{ apiHealthOpen: false }">
            <h3 class="text-sm font-semibold leading-6 text-gray-950 dark:text-white mb-4">API &amp; integrations</h3>
            <ul class="space-y-3">
                @foreach ($apis as $key => $api)
                    <li class="flex items-center justify-between gap-4 py-1 text-sm">
                        <span class="text-gray-700 dark:text-gray-300 min-w-0">
                            {{ $api['label'] }}
                            @if (!empty($api['detail']))
                                <span class="text-gray-500 dark:text-gray-400">({{ $api['detail'] }})</span>
                            @endif
                        </span>
                        @if ($api['configured'])
                            <span class="shrink-0 inline-flex items-center rounded-md bg-emerald-50 dark:bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-400">Configured</span>
                        @else
                            <span class="shrink-0 inline-flex items-center rounded-md bg-gray-200 dark:bg-gray-700 px-2.5 py-1 text-xs font-medium text-gray-600 dark:text-gray-400">Not configured</span>
                        @endif
                    </li>
                @endforeach
            </ul>
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                <button type="button" @click="apiHealthOpen = true" class="inline-flex items-center gap-2 rounded-lg bg-violet-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2">
                    View API health &amp; errors
                    @if ($api_has_errors ?? false)
                        <span class="inline-flex items-center rounded-full bg-red-500/90 px-2 py-0.5 text-xs font-medium text-white">Errors</span>
                    @endif
                </button>
            </div>
            {{-- API health modal (dark popup) --}}
            <div x-show="apiHealthOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true" role="dialog">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div x-show="apiHealthOpen" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-gray-950/70" @click="apiHealthOpen = false"></div>
                    <div x-show="apiHealthOpen" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="relative w-full max-w-2xl rounded-xl bg-gray-900 shadow-2xl ring-2 ring-gray-700">
                        <div class="p-6 text-white">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-semibold text-white">API health &amp; recent errors</h4>
                                <button type="button" @click="apiHealthOpen = false" class="flex h-10 w-10 items-center justify-center rounded-lg text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500" aria-label="Close">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                            @livewire(\App\Filament\Livewire\ApiHealthModalContent::class)
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- BigQuery sync --}}
        <section class="rounded-lg border border-gray-200 dark:border-gray-600/50 bg-gray-50/50 dark:bg-gray-800/30 px-5 py-5">
            <h3 class="text-sm font-semibold leading-6 text-gray-950 dark:text-white mb-4">BigQuery sync (daily)</h3>
            @if (!$bigquerySync['configured'])
                <p class="text-sm text-amber-600 dark:text-amber-400">Not configured (credentials, project, or dataset missing).</p>
            @elseif ($bigquerySync['last_run'] === null)
                <p class="text-sm text-gray-600 dark:text-gray-400">No sync run recorded yet. The job runs daily; run it manually from the queue or wait for the next schedule.</p>
            @else
                @php $run = $bigquerySync['last_run']; @endphp
                <div class="flex flex-wrap items-center gap-4 text-sm mb-3">
                    <span class="text-gray-500 dark:text-gray-400">Last run:</span>
                    <strong class="tabular-nums text-gray-900 dark:text-white">{{ $run['started_at'] }}</strong>
                    @if ($run['status'] === 'success')
                        <span class="inline-flex items-center rounded-md bg-emerald-50 dark:bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-400">Success</span>
                    @else
                        <span class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-500/10 px-2.5 py-1 text-xs font-medium text-red-700 dark:text-red-400">Failed</span>
                    @endif
                </div>
                @if (!empty($run['summary']) && is_array($run['summary']))
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Rows synced: @foreach ($run['summary'] as $t => $c) {{ $t }}: {{ $c }}@if (!$loop->last), @endif @endforeach</p>
                @endif
                @if ($run['status'] === 'failed' && !empty($run['message']))
                    <div class="rounded-lg border border-red-200 dark:border-red-900/50 bg-red-50/50 dark:bg-red-950/30 px-4 py-3 mt-2">
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ \Illuminate\Support\Str::limit($run['message'], 200) }}</p>
                        @if (!empty($run['error_detail']))
                            <details class="mt-2"><summary class="text-xs text-red-600 dark:text-red-400 cursor-pointer">Show details</summary><pre class="mt-1 text-xs text-gray-600 dark:text-gray-500 whitespace-pre-wrap break-all">{{ \Illuminate\Support\Str::limit($run['error_detail'], 500) }}</pre></details>
                        @endif
                    </div>
                @endif
            @endif
        </section>

        {{-- Scheduled tasks --}}
        <section class="rounded-lg border border-gray-200 dark:border-gray-600/50 bg-gray-50/50 dark:bg-gray-800/30 px-5 py-5">
            <h3 class="text-sm font-semibold leading-6 text-gray-950 dark:text-white mb-4">Scheduled tasks</h3>
            @if ($scheduledTasks['has_failures'] ?? false)
                <div class="mb-4 rounded-lg border border-red-200 dark:border-red-900/50 bg-red-50 dark:bg-red-950/30 px-4 py-3">
                    <p class="text-sm font-medium text-red-800 dark:text-red-200">One or more scheduled tasks have failed. Check the table and recent failures below.</p>
                </div>
            @endif
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Cron runs every minute: <code class="rounded bg-gray-200 dark:bg-gray-700 px-2 py-1 text-xs font-mono">* * * * * php artisan schedule:run</code>
            </p>
            @if (count($scheduledTasks['last_runs']) > 0)
                @if (($scheduledTasks['required_total'] ?? 0) > 0)
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        Required tasks: <strong>{{ $scheduledTasks['required_ok'] ?? 0 }}</strong> of {{ $scheduledTasks['required_total'] }} last run OK
                        @if (($scheduledTasks['required_failed'] ?? 0) > 0)
                            <span class="text-red-600 dark:text-red-400">({{ $scheduledTasks['required_failed'] }} failed)</span>
                        @endif
                    </p>
                @endif
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-600 -mx-px mb-4">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600 text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-800/50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300">Task</th>
                                <th scope="col" class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300">Last run</th>
                                <th scope="col" class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300 w-28">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600 bg-white dark:bg-gray-900/50">
                            @foreach ($scheduledTasks['last_runs'] as $run)
                                <tr>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                        <span class="font-medium">{{ \Illuminate\Support\Str::afterLast($run['task_display_name'], '\\') ?: $run['task_display_name'] }}</span>
                                        @if ($run['is_required'])
                                            <span class="ml-2 inline-flex items-center rounded bg-blue-50 dark:bg-blue-500/10 px-2 py-0.5 text-xs font-medium text-blue-700 dark:text-blue-400">Required</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400 tabular-nums">{{ $run['ran_at'] }}</td>
                                    <td class="px-4 py-3">
                                        @if ($run['status'] === 'finished')
                                            <span class="inline-flex items-center rounded-md bg-emerald-50 dark:bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-400">Success</span>
                                        @else
                                            <span class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-500/10 px-2.5 py-1 text-xs font-medium text-red-700 dark:text-red-400">Failed</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-amber-600 dark:text-amber-400 mb-4">No scheduled task runs recorded yet. Ensure cron is running <code class="rounded bg-gray-200 dark:bg-gray-700 px-2 py-1 text-xs font-mono">schedule:run</code>.</p>
            @endif
            @if (count($scheduledTasks['recent_failures']) > 0)
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-2">Recent failures</h4>
                    <ul class="space-y-2 text-sm text-red-700 dark:text-red-400">
                        @foreach ($scheduledTasks['recent_failures'] as $f)
                            <li class="flex flex-wrap gap-x-2 gap-y-0.5">
                                <span class="font-medium">{{ \Illuminate\Support\Str::afterLast($f['task_display_name'], '\\') ?: $f['task_display_name'] }}</span>
                                <span class="text-gray-500 dark:text-gray-400">{{ $f['ran_at'] }}</span>
                                @if (!empty($f['message']))
                                    <span class="w-full text-gray-600 dark:text-gray-500">— {{ \Illuminate\Support\Str::limit($f['message'], 80) }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </section>
    </div>
</div>
