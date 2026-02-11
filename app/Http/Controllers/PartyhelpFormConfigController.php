<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\BudgetRange;
use App\Models\GuestBracket;
use App\Models\OccasionType;
use App\Models\PricingMatrix;
use App\Models\VenueStyle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Serves form configuration for the WordPress Partyhelp form plugin.
 * Used for syncing areas, occasion types, guest brackets, and budget ranges.
 */
class PartyhelpFormConfigController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'areas' => $this->getAreas(),
            'occasion_types' => $this->getOccasionTypes(),
            'guest_brackets' => $this->getGuestBrackets(),
            'budget_ranges' => $this->getBudgetRanges(),
            'venue_styles' => $this->getVenueStyles(),
        ]);
    }

    public function areas(): JsonResponse
    {
        return response()->json(['areas' => $this->getAreas()]);
    }

    public function occasionTypes(): JsonResponse
    {
        return response()->json(['occasion_types' => $this->getOccasionTypes()]);
    }

    public function guestBrackets(): JsonResponse
    {
        return response()->json(['guest_brackets' => $this->getGuestBrackets()]);
    }

    public function budgetRanges(): JsonResponse
    {
        return response()->json(['budget_ranges' => $this->getBudgetRanges()]);
    }

    public function venueStyles(): JsonResponse
    {
        return response()->json(['venue_styles' => $this->getVenueStyles()]);
    }

    /**
     * Receive debug payloads from the WordPress plugin when debug mode is enabled.
     */
    public function debugLog(Request $request): JsonResponse
    {
        $payload = $request->all();
        Log::channel('single')->info('Partyhelp form plugin debug', [
            'site_url' => $payload['site_url'] ?? null,
            'plugin_version' => $payload['plugin_version'] ?? null,
            'entries' => $payload['entries'] ?? [],
        ]);

        return response()->json(['success' => true]);
    }

    private function getAreas(): array
    {
        if (! Schema::hasTable('areas')) {
            return [];
        }

        return Area::with('postcodes')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(function (Area $area) {
                $suburbs = $area->postcodes->pluck('suburb')->sort()->values()->toArray();

                return [
                    'id' => $area->id,
                    'name' => $area->name,
                    'label' => $suburbs ? "{$area->name} - " . implode(', ', $suburbs) : $area->name,
                    'suburbs' => $suburbs,
                ];
            })
            ->toArray();
    }

    private function getOccasionTypes(): array
    {
        $options = OccasionType::options();

        return collect($options)->map(fn (string $label, string $key) => [
            'key' => $key,
            'label' => $label,
        ])->values()->toArray();
    }

    private function getGuestBrackets(): array
    {
        return GuestBracket::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (GuestBracket $b) => [
                'value' => $b->value,
                'label' => $b->label,
                'guest_min' => $b->guest_min,
                'guest_max' => $b->guest_max, // null when bracket is "maximum limit" (no upper bound)
            ])
            ->values()
            ->all();
    }

    private function getBudgetRanges(): array
    {
        return BudgetRange::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'label', 'min_value', 'max_value'])
            ->map(fn ($r) => [
                'id' => $r->id,
                'label' => $r->label,
                'value' => $r->label,
            ])
            ->toArray();
    }

    /** Name => key used for lead room_styles and matching. */
    private const VENUE_STYLE_NAME_TO_KEY = [
        'Bar' => 'bar',
        'Function Room' => 'function_room',
        'Night Club' => 'club',
        'Courtyard' => 'semi_outdoor',
        'Lounge - Classy' => 'lounge_classy',
        'Pub' => 'pub',
    ];

    private function getVenueStyles(): array
    {
        try {
            if (! Schema::hasTable('venue_styles')) {
                Log::debug('Partyhelp form config: venue_styles table missing');

                return [];
            }

            $styles = VenueStyle::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            return $styles->map(function (VenueStyle $s) {
                $key = self::VENUE_STYLE_NAME_TO_KEY[$s->name] ?? Str::slug($s->name);
                $imageUrl = null;
                try {
                    if ($s->image_path) {
                        $imageUrl = $s->image_url;
                    }
                } catch (\Throwable $e) {
                    Log::warning('Partyhelp form config: venue_style image_url failed', [
                        'venue_style_id' => $s->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'key' => $key,
                    'image_url' => $imageUrl,
                    'sort_order' => (int) $s->sort_order,
                ];
            })->toArray();
        } catch (\Throwable $e) {
            Log::warning('Partyhelp form config: getVenueStyles failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [];
        }
    }
}
