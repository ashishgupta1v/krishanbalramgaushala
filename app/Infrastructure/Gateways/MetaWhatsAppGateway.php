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
    public function sendMessage(string $to, string $message): array
    {
        // 1. Ensure phone number has country code (default to '91' for India if 10 digits)
        $cleanNumber = preg_replace('/\D/', '', $to);
        if (strlen($cleanNumber) === 10) {
            $cleanNumber = '91' . $cleanNumber;
        }

        $url = "https://graph.facebook.com/v25.0/{$this->phoneNumberId}/messages";

        Log::info("Meta WA: Sending message to {$cleanNumber}...");

        try {
            // Determine if the message is a template or standard text
            // For general dynamic wishes/OTPs, we send them as a 'text' message
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

            $response = Http::withToken($this->accessToken)
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
