<?php

namespace App\Infrastructure\Gateways;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaWhatsAppGateway implements WhatsAppGateway
{
    protected string $phoneNumberId;
    protected string $accessToken;

    public function __construct()
    {
        $this->phoneNumberId = env('META_WA_PHONE_NUMBER_ID', '1115905564936723');
        $this->accessToken   = env('META_WA_ACCESS_TOKEN', '');
    }

    /**
     * Send a real WhatsApp message using Meta Cloud API.
     */
    public function sendMessage(string $to, string $message, ?string $templateName = null): array
    {
        // 1. Ensure phone number has country code (default to '91' for India if 10 digits)
        $cleanNumber = preg_replace('/\D/', '', $to);
        if (strlen($cleanNumber) === 10) {
            $cleanNumber = '91' . $cleanNumber;
        }

        $url = "https://graph.facebook.com/v25.0/{$this->phoneNumberId}/messages";

        Log::info("Meta WA: Sending message to {$cleanNumber}...");

        try {
            // If templateName is not explicitly passed, fallback to env()
            if (!$templateName) {
                $templateName = env('META_WA_TEMPLATE_NAME');
            }

            if ($templateName) {
                // Find devotee to get their name
                $devotee = \App\Domain\Devotee\Devotee::where('whatsapp', 'like', '%' . substr($cleanNumber, -10))->first();
                $name = $devotee ? $devotee->name : 'Devotee';

                // Determine components/parameters based on the template name
                if ($templateName === 'jaspers_market_order_confirmation_v1') {
                    $code = strval(random_int(100000, 999999));
                    $dateStr = now()->format('M d, Y');
                    $parameters = [
                        ['type' => 'text', 'text' => $name],
                        ['type' => 'text', 'text' => $code],
                        ['type' => 'text', 'text' => $dateStr],
                    ];
                } else {
                    // For standard approved templates, we pass the devotee's name
                    $parameters = [
                        ['type' => 'text', 'text' => $name],
                    ];
                }

                $payload = [
                    'messaging_product' => 'whatsapp',
                    'recipient_type'    => 'individual',
                    'to'                => $cleanNumber,
                    'type'              => 'template',
                    'template'          => [
                        'name'     => $templateName,
                        'language' => [
                            'code' => 'en_US'
                        ],
                        'components' => [
                            [
                                'type'       => 'body',
                                'parameters' => $parameters
                            ]
                        ]
                    ]
                ];
            } else {
                $payload = [
                    'messaging_product' => 'whatsapp',
                    'recipient_type'    => 'individual',
                    'to'                => $cleanNumber,
                    'type'              => 'text',
                    'text'              => [
                        'preview_url' => false,
                        'body'        => $message
                    ]
                ];
            }

            $response = Http::withoutVerifying()
                ->withToken($this->accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            if ($response->successful()) {
                $body = $response->json();
                Log::info("Meta WA: Send successful. Message ID: " . ($body['messages'][0]['id'] ?? 'N/A'));
                return [
                    'success' => true,
                    'msgid'   => $body['messages'][0]['id'] ?? null,
                    'error'   => null,
                ];
            }

            $errorMsg = $response->body();
            Log::error("Meta WA API Error: Status {$response->status()} | Body: {$errorMsg}");
            return [
                'success' => false,
                'msgid'   => null,
                'error'   => "Meta API Error: " . ($response->json('error.message') ?? 'Unknown error'),
            ];

        } catch (\Exception $e) {
            Log::error("Meta WA Exception: " . $e->getMessage());
            return [
                'success' => false,
                'msgid'   => null,
                'error'   => $e->getMessage(),
            ];
        }
    }
}
