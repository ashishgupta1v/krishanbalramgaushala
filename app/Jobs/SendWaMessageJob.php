<?php

namespace App\Jobs;

use App\Domain\Devotee\Devotee;
use App\Domain\Messaging\BroadcastLog;
use App\Infrastructure\Gateways\WhatsAppGateway;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class SendWaMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Devotee $devotee;
    protected string $message;
    protected ?string $broadcastId;
    protected ?string $wishLogId;
    protected ?string $templateName;

    /**
     * Create a new job instance.
     */
    public function __construct(Devotee $devotee, string $message, ?string $broadcastId = null, ?string $wishLogId = null, ?string $templateName = null)
    {
        $this->devotee     = $devotee;
        $this->message     = $message;
        $this->broadcastId = $broadcastId;
        $this->wishLogId   = $wishLogId;
        $this->templateName = $templateName;
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppGateway $gateway): void
    {
        // Replace {name} placeholder with devotee name
        $compiledMessage = str_replace('{name}', $this->devotee->name, $this->message);

        $result = $gateway->sendMessage($this->devotee->whatsapp, $compiledMessage, $this->templateName);

        if ($this->broadcastId) {
            $status = $result['success'] ? 'sent' : 'failed';
            BroadcastLog::create([
                'id'            => (string) Str::uuid(),
                'broadcast_id'  => $this->broadcastId,
                'devotee_id'    => $this->devotee->id,
                'status'        => $status,
                'error_message' => $result['error'] ?? null,
                'sent_at'       => now(),
            ]);

            // Update parent Broadcast counts and check completion
            $broadcast = \App\Domain\Messaging\Broadcast::find($this->broadcastId);
            if ($broadcast) {
                $column = $status === 'sent' ? 'sent_count' : 'failed_count';
                $broadcast->increment($column);
                
                $broadcast->refresh();
                $processed = $broadcast->sent_count + $broadcast->failed_count;
                if ($processed >= $broadcast->total_count) {
                    $broadcast->update([
                        'status'  => 'done',
                        'sent_at' => now(),
                    ]);
                }
            }
        }

        if ($this->wishLogId) {
            $log = \App\Domain\Messaging\WishDeliveryLog::find($this->wishLogId);
            if ($log) {
                $log->update([
                    'status'        => $result['success'] ? 'sent' : 'failed',
                    'error_message' => $result['error'] ?? null,
                    'sent_at'       => now(),
                ]);
            }
        }
    }
}
