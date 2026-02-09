<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postcode extends Model
{
    protected $fillable = [
        'suburb', 'postcode', 'state', 'sort_order',
    ];

    public function areas()
    {
        return $this->belongsToMany(Area::class, 'area_postcode');
    }

    public static function optionsForSelect(): array
    {
        return static::orderBy('suburb')
            ->get()
            ->mapWithKeys(fn (self $p) => [$p->id => "{$p->suburb} ({$p->postcode})"])
            ->toArray();
    }
}
