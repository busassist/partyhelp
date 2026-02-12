<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiHealthEntry extends Model
{
    public $timestamps = false;

    protected $fillable = ['service', 'message', 'context', 'created_at'];

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'created_at' => 'datetime',
        ];
    }
}
