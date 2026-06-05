<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebhookController extends Controller
{
    /**
     * Handle WhatsApp delivery receipt webhooks.
     * The WA provider will POST delivery status updates here.
     */
    public function whatsapp(Request $request)
    {
        // TODO: Parse delivery receipts and update BroadcastLogs
        // Example: Gupshup sends { msgid, status, timestamp }
        \Log::info('WA Webhook received', $request->all());
        return response()->json(['ok' => true]);
    }
}
