<?php

namespace App\Domain\Messaging;

use App\Domain\Devotee\Devotee;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WishDeliveryLog extends Model
{
    use HasUuids;

    protected $table = 'wish_delivery_logs';

    protected $fillable = [
        'devotee_id',
        'wish_type',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function devotee(): BelongsTo
    {
        return $this->belongsTo(Devotee::class, 'devotee_id');
    }
}
