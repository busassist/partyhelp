<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdditionalServiceSubmission extends Model
{
    protected $fillable = ['lead_id', 'submitted_at', 'selected_service_ids'];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'selected_service_ids' => 'array',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /** @return array<int, string> service id => name */
    public function getSelectedServiceNamesAttribute(): array
    {
        $ids = $this->selected_service_ids ?? [];
        if ($ids === []) {
            return [];
        }

        return AdditionalService::whereIn('id', $ids)
            ->pluck('name', 'id')
            ->all();
    }

    public function getRespondentEmailAttribute(): string
    {
        return $this->lead?->email ?? '';
    }
}
