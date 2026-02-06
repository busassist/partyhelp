<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetRange extends Model
{
    protected $fillable = [
        'label', 'min_value', 'max_value',
        'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'min_value' => 'decimal:2',
            'max_value' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
