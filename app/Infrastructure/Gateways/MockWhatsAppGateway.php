<?php

namespace App\Infrastructure\Gateways;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MockWhatsAppGateway implements WhatsAppGateway
{
    /**
     * Simulate sending a WhatsApp message by logging it.
     */
    public function sendMessage(string $to, string $message, ?string $templateName = null): array
    {
        // Simulate a tiny network latency (e.g. 50-100ms)
        usleep(random_int(20, 80) * 1000);

        $tmplInfo = $templateName ? " | Template: {$templateName}" : "";
        Log::info("WhatsApp Send Mock: To: {$to} | Message: {$message}{$tmplInfo}");

        return [
            'success' => true,
            'msgid'   => 'mock_' . Str::random(16),
            'error'   => null,
        ];
    }
}
