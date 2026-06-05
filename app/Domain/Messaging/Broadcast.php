<?php

namespace App\Domain\Messaging;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Broadcast extends Model
{
    use HasUuids;

    protected $fillable = [
        'template_id', 'message_body', 'recipient_mode',
        'total_count', 'sent_count', 'failed_count',
        'status', 'scheduled_at', 'sent_at'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at'      => 'datetime',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(MessageTemplate::class, 'template_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(BroadcastLog::class, 'broadcast_id');
    }
}
