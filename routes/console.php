<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new \App\Jobs\DailyWishJob)->dailyAt('07:00')->timezone('Asia/Kolkata');

Schedule::call(function () {
    $pending = \App\Domain\Automation\FbPost::where('status', 'scheduled')
        ->where('scheduled_at', '<=', now())
        ->get();
        
    $fbGateway = app(\App\Infrastructure\Gateways\FacebookGateway::class);
    
    foreach ($pending as $post) {
        $result = $fbGateway->publishPost($post->content);
        $post->update([
            'status'     => $result['success'] ? 'sent' : 'failed',
            'fb_post_id' => $result['post_id'] ?? null,
            'posted_at'  => $result['success'] ? now() : null,
        ]);
    }
})->everyMinute();


