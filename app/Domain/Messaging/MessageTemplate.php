<?php

namespace App\Domain\Messaging;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    use HasUuids;

    protected $fillable = ['key', 'label', 'body', 'variables', 'meta_name', 'status', 'category', 'is_active_for'];
    protected $casts    = ['variables' => 'array'];
}
