<?php

namespace App\Infrastructure\Gateways;

interface WhatsAppGateway
{
    /**
     * Send a WhatsApp message to a recipient.
     *
     * @param string $to The devotee's WhatsApp number (10-digit or E.164)
     * @param string $message The compiled message text
     * @return array ['success' => bool, 'msgid' => ?string, 'error' => ?string]
     */
    public function sendMessage(string $to, string $message): array;
}
