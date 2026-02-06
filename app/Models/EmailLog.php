<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EmailLog extends Model
{
    protected $table = 'email_log';

    protected $fillable = [
        'to_email', 'to_name', 'subject', 'template',
        'merge_data', 'status', 'sendgrid_message_id',
        'related_type', 'related_id',
    ];

    protected function casts(): array
    {
        return [
            'merge_data' => 'array',
        ];
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }
}
