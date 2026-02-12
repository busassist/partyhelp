<?php

namespace App\Services;

use App\Models\DebugLogEntry;
use App\Models\Lead;
use App\Models\Venue;
use App\Models\SystemSetting;
use Illuminate\Support\Collection;

class DebugLogService
{
    public static function isEnabled(): bool
    {
        return (bool) SystemSetting::get('debug_logging_enabled', false);
    }

    public static function logLeadReceived(Lead $lead): void
    {
        if (! self::isEnabled()) {
            return;
        }

        $location = self::leadLocationDisplay($lead);

        DebugLogEntry::create([
            'type' => 'lead_received',
            'payload' => [
                'lead_id' => $lead->id,
                'name' => trim($lead->first_name . ' ' . $lead->last_name),
                'email' => $lead->email,
                'location' => $location,
            ],
        ]);
    }

    /**
     * @param  Collection<int, string>|array<int, string>  $venueNames
     */
    public static function logVenuesMatched(Lead $lead, int $count, Collection|array $venueNames): void
    {
        if (! self::isEnabled()) {
            return;
        }

        $names = $venueNames instanceof Collection
            ? $venueNames->values()->all()
            : array_values($venueNames);

        DebugLogEntry::create([
            'type' => 'venues_matched',
            'payload' => [
                'lead_id' => $lead->id,
                'count' => $count,
                'venue_names' => $names,
            ],
        ]);
    }

    public static function logEmailSent(string $emailType, array $context = []): void
    {
        if (! self::isEnabled()) {
            return;
        }

        DebugLogEntry::create([
            'type' => 'email_sent',
            'payload' => array_merge(['email' => $emailType], $context),
        ]);
    }

    public static function logVenueApprovalQueued(Venue $venue): void
    {
        if (! self::isEnabled()) {
            return;
        }

        DebugLogEntry::create([
            'type' => 'venue_approval_queued',
            'payload' => [
                'venue_id' => $venue->id,
                'business_name' => $venue->business_name,
                'contact_email' => $venue->contact_email,
            ],
        ]);
    }

    /**
     * Location display: area names only. If suburbs only ticked, show parent area(s).
     */
    private static function leadLocationDisplay(Lead $lead): string
    {
        $selections = $lead->location_selections;
        if (is_array($selections) && count($selections) > 0) {
            $areas = [];
            foreach ($selections as $item) {
                $type = $item['type'] ?? null;
                $area = trim((string) ($item['area'] ?? ''));
                $name = trim((string) ($item['name'] ?? ''));
                if ($type === 'area' && $name !== '') {
                    $areas[$name] = true;
                } elseif ($type === 'suburb' && $area !== '') {
                    $areas[$area] = true;
                } elseif ($name !== '') {
                    $areas[$name] = true;
                }
            }

            return implode(', ', array_keys($areas));
        }

        $suburb = $lead->suburb;
        if (is_array($suburb)) {
            return implode(', ', array_map('trim', $suburb));
        }

        return $suburb ? trim((string) $suburb) : '';
    }
}
