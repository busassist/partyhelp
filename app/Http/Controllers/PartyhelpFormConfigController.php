<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\BudgetRange;
use App\Models\OccasionType;
use App\Models\PricingMatrix;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;

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
        $brackets = PricingMatrix::select('guest_min', 'guest_max')
            ->where('is_active', true)
            ->distinct()
            ->orderBy('guest_min')
            ->get();

        if ($brackets->isEmpty()) {
            return [
                ['value' => '10-29', 'label' => '10-29', 'guest_min' => 10, 'guest_max' => 29],
                ['value' => '30-60', 'label' => '30-60', 'guest_min' => 30, 'guest_max' => 60],
                ['value' => '61-100', 'label' => '61-100', 'guest_min' => 61, 'guest_max' => 100],
                ['value' => '100+', 'label' => '100+', 'guest_min' => 101, 'guest_max' => 500],
            ];
        }

        $merged = [];
        foreach ($brackets as $row) {
            $label = $row->guest_min === $row->guest_max
                ? (string) $row->guest_min
                : "{$row->guest_min}-{$row->guest_max}";
            $key = "{$row->guest_min}-{$row->guest_max}";
            if (! isset($merged[$key])) {
                $merged[$key] = [
                    'value' => $key,
                    'label' => $label,
                    'guest_min' => $row->guest_min,
                    'guest_max' => $row->guest_max,
                ];
            }
        }

        return array_values($merged);
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
}
