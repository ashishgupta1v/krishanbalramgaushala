<?php

namespace App\Domain\Automation;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class FbPost extends Model
{
    use HasUuids;

    protected $table    = 'fb_posts';
    protected $fillable = ['type', 'content', 'devotee_count', 'status', 'fb_post_id', 'scheduled_at', 'posted_at'];
    protected $casts    = ['scheduled_at' => 'datetime', 'posted_at' => 'datetime'];
}
