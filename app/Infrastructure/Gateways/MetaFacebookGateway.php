<?php

namespace App\Infrastructure\Gateways;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaFacebookGateway implements FacebookGateway
{
    protected string $pageId;
    protected string $pageAccessToken;

    public function __construct()
    {
        $this->pageId          = env('FB_PAGE_ID', '');
        $this->pageAccessToken = env('FB_PAGE_ACCESS_TOKEN', '');
    }

    /**
     * Publish a post to the Facebook page feed.
     */
    public function publishPost(string $content): array
    {
        if (empty($this->pageId) || empty($this->pageAccessToken)) {
            Log::warning("Meta FB: Credentials not set. Simulating post...");
            return [
                'success' => false,
                'post_id' => null,
                'error'   => 'Facebook credentials (FB_PAGE_ID / FB_PAGE_ACCESS_TOKEN) are missing.',
            ];
        }

        $url = "https://graph.facebook.com/v25.0/{$this->pageId}/feed";

        Log::info("Meta FB: Publishing post to page {$this->pageId}...");

        try {
            $response = Http::post($url, [
                'message'      => $content,
                'access_token' => $this->pageAccessToken,
            ]);

            if ($response->successful()) {
                $body = $response->json();
                Log::info("Meta FB: Post published successfully. ID: " . ($body['id'] ?? 'N/A'));
                return [
                    'success' => true,
                    'post_id' => $body['id'] ?? null,
                    'error'   => null,
                ];
            }

            $errorMsg = $response->body();
            Log::error("Meta FB API Error: Status {$response->status()} | Body: {$errorMsg}");
            return [
                'success' => false,
                'post_id' => null,
                'error'   => "Meta FB API Error: " . ($response->json('error.message') ?? 'Unknown error'),
            ];

        } catch (\Exception $e) {
            Log::error("Meta FB Exception: " . $e->getMessage());
            return [
                'success' => false,
                'post_id' => null,
                'error'   => $e->getMessage(),
            ];
        }
    }
}
