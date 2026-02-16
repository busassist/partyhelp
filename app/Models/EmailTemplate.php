<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'key', 'name', 'subject', 'content_slots',
        'sendgrid_template_id', 'content_hash',
        'send_via_whatsapp',
        'whatsapp_body', 'whatsapp_accept_label', 'whatsapp_ignore_label', 'twilio_content_sid',
    ];

    protected function casts(): array
    {
        return [
            'content_slots' => 'array',
            'send_via_whatsapp' => 'boolean',
        ];
    }

    public function getSlot(string $key, string $default = ''): string
    {
        return (string) ($this->content_slots[$key] ?? $default);
    }

    public function setSlot(string $key, string $value): void
    {
        $slots = $this->content_slots ?? [];
        $slots[$key] = $value;
        $this->content_slots = $slots;
    }

    public static function byKey(string $key): ?self
    {
        return static::where('key', $key)->first();
    }
}
