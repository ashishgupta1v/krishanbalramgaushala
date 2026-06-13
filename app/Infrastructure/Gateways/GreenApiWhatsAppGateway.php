<?php

namespace App\Infrastructure\Gateways;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GreenApiWhatsAppGateway implements WhatsAppGateway
{
    protected string $apiUrl;
    protected string $idInstance;
    protected string $token;

    public function __construct()
    {
        $this->apiUrl     = config('services.greenapi.url', 'https://api.green-api.com');
        $this->idInstance = config('services.greenapi.id', '');
        $this->token      = config('services.greenapi.token', '');
    }

    /**
     * Send a real WhatsApp message using Green-API.
     */
    public function sendMessage(string $to, string $message, ?string $templateName = null): array
    {
        // 1. Clean the phone number (digits only)
        $cleanNumber = preg_replace('/\D/', '', $to);
        if (strlen($cleanNumber) === 10) {
            $cleanNumber = '91' . $cleanNumber;
        }

        // Green-API requires chatId in format phone_number@c.us
        $chatId = $cleanNumber . '@c.us';

        $url = "{$this->apiUrl}/waInstance{$this->idInstance}/sendMessage/{$this->token}";

        Log::info("Green-API: Sending message to {$chatId}...");

        try {
            $payload = [
                'chatId'  => $chatId,
                'message' => $message,
            ];

            $response = Http::withoutVerifying()
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            if ($response->successful()) {
                $body = $response->json();
                Log::info("Green-API: Send successful. Message ID: " . ($body['idMessage'] ?? 'N/A'));
                return [
                    'success' => true,
                    'msgid'   => $body['idMessage'] ?? null,
                    'error'   => null,
                ];
            }

            $errorMsg = $response->body();
            Log::error("Green-API Error: Status {$response->status()} | Body: {$errorMsg}");
            return [
                'success' => false,
                'msgid'   => null,
                'error'   => "Green-API Error: Status " . $response->status() . " | " . ($response->json('message') ?? 'Unknown error'),
            ];

        } catch (\Exception $e) {
            Log::error("Green-API Exception: " . $e->getMessage());
            return [
                'success' => false,
                'msgid'   => null,
                'error'   => $e->getMessage(),
            ];
        }
    }
}
