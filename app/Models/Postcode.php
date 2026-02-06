<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postcode extends Model
{
    protected $fillable = [
        'suburb', 'postcode', 'state', 'sort_order',
    ];

    public static function options(): array
    {
        return static::orderBy('suburb')
            ->get()
            ->mapWithKeys(fn (self $p) => [$p->suburb => "{$p->suburb} ({$p->postcode})"])
            ->toArray();
    }
}
