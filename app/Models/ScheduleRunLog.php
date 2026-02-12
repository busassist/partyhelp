<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleRunLog extends Model
{
    protected $fillable = [
        'task_key',
        'task_display_name',
        'status',
        'message',
        'ran_at',
    ];

    protected function casts(): array
    {
        return [
            'ran_at' => 'datetime',
        ];
    }

    public function isSuccess(): bool
    {
        return $this->status === 'finished';
    }
}
