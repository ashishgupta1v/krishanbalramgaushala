<?php

namespace App\Domain\Messaging;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class BroadcastLog extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = ['broadcast_id', 'devotee_id', 'status', 'error_message', 'sent_at'];
    protected $casts    = ['sent_at' => 'datetime'];
}
