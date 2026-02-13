<?php

namespace App\Services;

use App\Models\Area;
use App\Models\Lead;
use App\Models\LeadMatch;
use App\Models\LeadPurchase;
use App\Models\CreditTransaction;
use App\Models\Venue;
use Illuminate\Support\Carbon;

/** Builds flattened row arrays for BigQuery sync (docs/BIGQUERY_SYNC_PROPOSAL.md). */
class BigQuerySyncDataBuilder
{
    private string $syncDate;

    /** @var array<string, string> occasion_type key => label */
    private array $occasionLabels;

    public function __construct()
    {
        $this->syncDate = now()->format('Y-m-d');
        $this->occasionLabels = \App\Models\OccasionType::options();
        if (empty($this->occasionLabels)) {
            $this->occasionLabels = config('partyhelp.occasion_types', []);
        }
    }

    private function occasionLabel(string $key): string
    {
        return $this->occasionLabels[$key] ?? $key;
    }

    /** @return array<int, array<string, mixed>> */
    public function phLeads(): array
    {
        $rows = [];
        foreach (Lead::query()->orderBy('id')->get() as $lead) {
            $rows[] = [
                'lead_id' => (int) $lead->id,
                'occasion_type' => $lead->occasion_type,
                'occasion_label' => $this->occasionLabel($lead->occasion_type),
                'guest_count' => (int) $lead->guest_count,
                'preferred_date' => $lead->preferred_date?->format('Y-m-d'),
                'suburb' => $lead->suburb ?? '',
                'status' => $lead->status,
                'base_price' => (float) $lead->base_price,
                'current_price' => (float) $lead->current_price,
                'discount_percent' => (int) $lead->discount_percent,
                'purchase_target' => (int) $lead->purchase_target,
                'purchase_count' => (int) $lead->purchase_count,
                'distributed_at' => $this->ts($lead->distributed_at),
                'fulfilled_at' => $this->ts($lead->fulfilled_at),
                'expires_at' => $this->ts($lead->expires_at),
                'created_at' => $this->ts($lead->created_at),
                'sync_date' => $this->syncDate,
            ];
        }

        return $rows;
    }

    /** @return array<int, array<string, mixed>> */
    public function phVenues(): array
    {
        $areas = Area::query()->get()->keyBy('id');
        $rows = [];
        foreach (Venue::query()->with('area')->orderBy('id')->get() as $venue) {
            $rows[] = [
                'venue_id' => (int) $venue->id,
                'business_name' => $venue->business_name ?? '',
                'area_id' => $venue->area_id ? (int) $venue->area_id : null,
                'area_name' => $venue->area?->name ?? ($venue->area_id ? $areas->get($venue->area_id)?->name ?? '' : ''),
                'suburb' => $venue->suburb ?? '',
                'state' => $venue->state ?? '',
                'postcode' => $venue->postcode ?? '',
                'status' => $venue->status,
                'credit_balance' => (float) $venue->credit_balance,
                'approved_at' => $this->ts($venue->approved_at),
                'last_activity_at' => $this->ts($venue->last_activity_at),
                'created_at' => $this->ts($venue->created_at),
                'sync_date' => $this->syncDate,
            ];
        }

        return $rows;
    }

    /** @return array<int, array<string, mixed>> */
    public function phLeadPurchases(): array
    {
        $rows = [];
        foreach (LeadPurchase::query()->with(['lead', 'venue', 'venue.area'])->orderBy('id')->get() as $p) {
            $lead = $p->lead;
            $venue = $p->venue;
            $rows[] = [
                'lead_purchase_id' => (int) $p->id,
                'lead_id' => (int) $p->lead_id,
                'venue_id' => (int) $p->venue_id,
                'occasion_type' => $lead?->occasion_type ?? '',
                'occasion_label' => $lead ? $this->occasionLabel($lead->occasion_type) : '',
                'preferred_date' => $lead?->preferred_date?->format('Y-m-d'),
                'guest_count' => $lead ? (int) $lead->guest_count : null,
                'lead_suburb' => $lead?->suburb ?? '',
                'lead_status' => $p->lead_status,
                'venue_business_name' => $venue?->business_name ?? '',
                'venue_area_name' => $venue?->area?->name ?? '',
                'amount_paid' => (float) $p->amount_paid,
                'discount_percent' => (int) $p->discount_percent,
                'purchased_at' => $this->ts($p->created_at),
                'sync_date' => $this->syncDate,
            ];
        }

        return $rows;
    }

    /** @return array<int, array<string, mixed>> */
    public function phCreditTransactions(): array
    {
        $rows = [];
        foreach (CreditTransaction::query()->with('venue')->orderBy('id')->get() as $t) {
            $rows[] = [
                'credit_transaction_id' => (int) $t->id,
                'venue_id' => (int) $t->venue_id,
                'venue_business_name' => $t->venue?->business_name ?? '',
                'type' => $t->type,
                'amount' => (float) $t->amount,
                'balance_after' => (float) $t->balance_after,
                'lead_purchase_id' => $t->lead_purchase_id ? (int) $t->lead_purchase_id : null,
                'stripe_payment_intent_id' => $t->stripe_payment_intent_id,
                'description' => $t->description,
                'admin_note' => $t->admin_note,
                'created_at' => $this->ts($t->created_at),
                'sync_date' => $this->syncDate,
            ];
        }

        return $rows;
    }

    /** @return array<int, array<string, mixed>> */
    public function phLeadMatches(): array
    {
        $rows = [];
        foreach (LeadMatch::query()->with('venue')->orderBy('id')->get() as $m) {
            $rows[] = [
                'lead_match_id' => (int) $m->id,
                'lead_id' => (int) $m->lead_id,
                'venue_id' => (int) $m->venue_id,
                'venue_business_name' => $m->venue?->business_name ?? '',
                'match_score' => (float) $m->match_score,
                'status' => $m->status,
                'notified_at' => $this->ts($m->notified_at),
                'viewed_at' => $this->ts($m->viewed_at),
                'purchased_at' => $this->ts($m->purchased_at),
                'sync_date' => $this->syncDate,
            ];
        }

        return $rows;
    }

    private function ts($dt): ?string
    {
        if ($dt instanceof Carbon) {
            return $dt->format('Y-m-d\TH:i:s.u\Z');
        }
        if ($dt instanceof \DateTimeInterface) {
            return $dt->format('Y-m-d\TH:i:s.u\Z');
        }

        return null;
    }
}
