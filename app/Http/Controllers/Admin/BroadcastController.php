<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Devotee\Devotee;
use App\Domain\Messaging\MessageTemplate;
use App\Domain\Messaging\Broadcast;
use App\Jobs\SendWaMessageJob;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BroadcastController extends Controller
{
    public function index(): Response
    {
        $templates = MessageTemplate::all()->map(fn ($t) => [
            'key'   => $t->key,
            'label' => $t->label,
            'body'  => $t->body,
        ]);

        return Inertia::render('Admin/Broadcast', [
            'templates'   => $templates,
            'totalCount'  => Devotee::count(),
            'activeCount' => Devotee::active()->count(),
            'activeTab'   => 'broadcast',
        ]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'message'      => 'required|string',
            'mode'         => 'required|in:all,active',
            'template_key' => 'nullable|string',
        ]);

        $devotees = $request->mode === 'active'
            ? Devotee::active()->get()
            : Devotee::all();

        // Find the template meta name if provided
        $metaName = null;
        if ($request->template_key) {
            $template = MessageTemplate::where('key', $request->template_key)->first();
            if ($template) {
                $metaName = $template->meta_name;
            }
        }

        // Create a Broadcast record
        $broadcast = Broadcast::create([
            'id'             => (string) Str::uuid(),
            'message_body'   => $request->message,
            'recipient_mode' => $request->mode,
            'total_count'    => $devotees->count(),
            'sent_count'     => 0,
            'failed_count'   => 0,
            'status'         => 'sending',
            'scheduled_at'   => now(),
        ]);

        // Dispatch background queue jobs
        $devotees->each(fn ($d) => SendWaMessageJob::dispatch($d, $request->message, $broadcast->id, null, $metaName));

        // If recipient list is empty, mark as done immediately
        if ($devotees->isEmpty()) {
            $broadcast->update([
                'status'  => 'done',
                'sent_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'count'   => $devotees->count(),
            'message' => "Queued broadcast for {$devotees->count()} devotees.",
        ]);
    }

    public function history(): Response
    {
        return Inertia::render('Admin/Broadcast', ['activeTab' => 'broadcast']);
    }
}
