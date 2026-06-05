<?php

namespace App\Domain\GaushalEvent;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GaushalEvent extends Model
{
    use HasUuids, SoftDeletes;

    protected $table    = 'gaushala_events';
    protected $fillable = ['title', 'description', 'icon', 'type', 'scheduled_at', 'time_label', 'is_recurring', 'recurrence_rule'];
    protected $casts    = ['scheduled_at' => 'datetime', 'is_recurring' => 'boolean'];
}
