<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DebugLogEntry extends Model
{
    public $timestamps = false;

    protected $fillable = ['type', 'payload'];

    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime',
    ];
}
