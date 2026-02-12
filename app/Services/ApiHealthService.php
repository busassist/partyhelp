<?php

namespace App\Services;

use App\Models\ApiHealthEntry;

class ApiHealthService
{
    public static function logError(string $service, string $message, array $context = []): void
    {
        ApiHealthEntry::create([
            'service' => $service,
            'message' => \Illuminate\Support\Str::limit($message, 1000),
            'context' => $context ?: null,
            'created_at' => now(),
        ]);

        // Keep only last 100 per service to avoid table bloat
        $ids = ApiHealthEntry::query()
            ->where('service', $service)
            ->orderByDesc('id')
            ->skip(100)
            ->pluck('id');
        if ($ids->isNotEmpty()) {
            ApiHealthEntry::whereIn('id', $ids)->delete();
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, ApiHealthEntry>
     */
    public static function recentErrors(int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return ApiHealthEntry::query()
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public static function hasRecentErrors(string $service = null): bool
    {
        $q = ApiHealthEntry::query();
        if ($service !== null) {
            $q->where('service', $service);
        }

        return $q->where('created_at', '>=', now()->subDays(7))->exists();
    }
}
