<?php

namespace App\Infrastructure\Gateways;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MockFacebookGateway implements FacebookGateway
{
    /**
     * Simulate publishing a Facebook page post.
     */
    public function publishPost(string $content): array
    {
        usleep(random_int(30, 90) * 1000);

        Log::info("Facebook Publish Mock: Content:\n{$content}");

        return [
            'success' => true,
            'post_id' => 'mock_fb_' . Str::random(16),
            'error'   => null,
        ];
    }
}
