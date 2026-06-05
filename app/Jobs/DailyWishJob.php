<?php

namespace App\Jobs;

use App\Domain\Devotee\Devotee;
use App\Domain\Automation\FbPost;
use App\Domain\Messaging\MessageTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DailyWishJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Run the automated daily birthday and anniversary wishing engine.
     */
    public function handle(\App\Infrastructure\Gateways\FacebookGateway $fbGateway): void
    {
        Log::info("DailyWishJob: Automated daily check started.");

        $birthdays    = Devotee::active()->birthdayToday()->get();
        $anniversaries = Devotee::active()->anniversaryToday()->get();

        Log::info("DailyWishJob: Found {$birthdays->count()} birthdays and {$anniversaries->count()} anniversaries.");

        // 1. Send Birthday Wishes on WhatsApp
        $bdayTemplate = MessageTemplate::where('is_active_for', 'birthday')->where('status', 'approved')->first() 
            ?? MessageTemplate::where('key', 'birthday')->first();
        $bdayBody     = $bdayTemplate?->body ?? "Happy Birthday {name}! May Lord Krishna bless you with abundant joy, health, and prosperity. Received from Krishan Balram Gaushala.";

        foreach ($birthdays as $devotee) {
            $log = \App\Domain\Messaging\WishDeliveryLog::create([
                'id'         => (string) \Illuminate\Support\Str::uuid(),
                'devotee_id' => $devotee->id,
                'wish_type'  => 'birthday',
                'status'     => 'pending',
            ]);
            SendWaMessageJob::dispatch($devotee, $bdayBody, null, $log->id);
        }

        // 2. Send Anniversary Wishes on WhatsApp
        $annTemplate = MessageTemplate::where('is_active_for', 'anniversary')->where('status', 'approved')->first()
            ?? MessageTemplate::where('key', 'anniversary')->first();
        $annBody     = $annTemplate?->body ?? "Happy Marriage Anniversary {name}! May Lord Krishna shower his divine blessings on your family. Received from Krishan Balram Gaushala.";

        foreach ($anniversaries as $devotee) {
            $log = \App\Domain\Messaging\WishDeliveryLog::create([
                'id'         => (string) \Illuminate\Support\Str::uuid(),
                'devotee_id' => $devotee->id,
                'wish_type'  => 'anniversary',
                'status'     => 'pending',
            ]);
            SendWaMessageJob::dispatch($devotee, $annBody, null, $log->id);
        }

        // 3. Automated Facebook Post
        if (cache('fb_auto_enabled', true) && ($birthdays->isNotEmpty() || $anniversaries->isNotEmpty())) {
            $this->postToFacebook($fbGateway, $birthdays, $anniversaries);
        }
    }

    /**
     * Create Facebook post for birthdays and anniversaries.
     */
    protected function postToFacebook(\App\Infrastructure\Gateways\FacebookGateway $fbGateway, $birthdays, $anniversaries): void
    {
        $names = [];
        foreach ($birthdays as $b) {
            $names[] = "🎂 " . $b->name;
        }
        foreach ($anniversaries as $a) {
            $names[] = "💍 " . $a->name;
        }

        $content = "🙏 JAI SHRI KRISHNA 🙏\n\n";
        $content .= "Krishan Balram Gaushala, Singla Enclave, Village Dullon Khurd, Pakhowal Road, Ludhiana offers special prayers and blessings today for our beloved devotees celebrating their special days:\n\n";
        $content .= implode("\n", $names) . "\n\n";
        $content .= "May Lord Krishna and Gomata bless you and your families with infinite health, happiness, and devotion. Hare Krishna! 🐄🪔🌸";

        // Post to Facebook Page feed
        $result = $fbGateway->publishPost($content);

        // Create database record
        $post = FbPost::create([
            'id'            => (string) Str::uuid(),
            'type'          => 'birthday',
            'content'       => $content,
            'devotee_count' => $birthdays->count() + $anniversaries->count(),
            'status'        => $result['success'] ? 'sent' : 'failed',
            'fb_post_id'    => $result['post_id'] ?? null,
            'scheduled_at'  => now(),
            'posted_at'     => $result['success'] ? now() : null,
        ]);

        Log::info("DailyWishJob: Automated Facebook post generated and processed. Status: {$post->status}, ID: {$post->fb_post_id}");
    }
}
