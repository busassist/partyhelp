<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SmsLog extends Model
{
    protected $table = 'sms_log';

    protected $fillable = [
        'to_phone', 'message', 'status',
        'provider_message_id', 'related_type', 'related_id',
    ];

    public function related(): MorphTo
    {
        return $this->morphTo();
    }
}
