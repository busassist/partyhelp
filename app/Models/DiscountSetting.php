<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountSetting extends Model
{
    protected $fillable = [
        'hours_elapsed', 'minutes_elapsed', 'discount_percent',
        'resend_notification', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'resend_notification' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
