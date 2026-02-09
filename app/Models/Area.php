<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = ['name', 'sort_order'];

    public function postcodes()
    {
        return $this->belongsToMany(Postcode::class, 'area_postcode');
    }

    public function venues()
    {
        return $this->hasMany(Venue::class);
    }
}
