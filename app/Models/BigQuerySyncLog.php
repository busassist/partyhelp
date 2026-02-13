<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BigQuerySyncLog extends Model
{
    protected $fillable = [
        'status', 'message', 'error_detail', 'summary',
        'started_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'summary' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    /** Latest sync run (success or failed). */
    public static function latestRun(): ?self
    {
        return static::query()->orderByDesc('started_at')->first();
    }
}
