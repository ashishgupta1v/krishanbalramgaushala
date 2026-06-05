<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Messaging\WishDeliveryLog;
use App\Domain\Messaging\MessageTemplate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;

class WishController extends Controller
{
    public function index(): Response
    {
        $logs = WishDeliveryLog::with('devotee')
            ->orderByDesc('created_at')
            ->limit(100)
            ->get()
            ->map(fn ($l) => [
                'id'            => $l->id,
                'devotee'       => $l->devotee ? [
                    'name'     => $l->devotee->name,
                    'whatsapp' => $l->devotee->whatsapp,
                ] : null,
                'wish_type'     => $l->wish_type,
                'status'        => $l->status,
                'error_message' => $l->error_message,
                'sent_at'       => $l->sent_at?->format('Y-m-d H:i:s'),
                'created_at'    => $l->created_at?->format('Y-m-d H:i:s'),
            ]);

        $templates = MessageTemplate::orderBy('key')
            ->get()
            ->map(fn ($t) => [
                'id'            => $t->id,
                'key'           => $t->key,
                'label'         => $t->label,
                'body'          => $t->body,
                'variables'     => $t->variables,
                'meta_name'     => $t->meta_name,
                'status'        => $t->status,
                'category'      => $t->category,
                'is_active_for' => $t->is_active_for,
            ]);

        return Inertia::render('Admin/Wishes', [
            'logs'      => $logs,
            'templates' => $templates,
            'activeTab' => 'wishes',
        ]);
    }

    public function submitTemplate(Request $request)
    {
        $validated = $request->validate([
            'label'     => 'required|string|max:100',
            'meta_name' => 'required|string|max:100|unique:message_templates,meta_name',
            'body'      => 'required|string',
            'category'  => 'required|string',
        ]);

        $key = 'custom_' . Str::slug($validated['label'], '_');

        MessageTemplate::create([
            'id'            => (string) Str::uuid(),
            'key'           => $key,
            'label'         => $validated['label'],
            'body'          => $validated['body'],
            'meta_name'     => $validated['meta_name'],
            'status'        => 'pending',
            'category'      => $validated['category'],
            'variables'     => ['name'],
            'is_active_for' => null,
        ]);

        return back()->with('success', 'Template submitted to Meta. Awaiting approval.');
    }

    public function updateTemplate(Request $request, string $id)
    {
        $template = MessageTemplate::findOrFail($id);

        $validated = $request->validate([
            'label'     => 'required|string|max:100',
            'body'      => 'required|string',
            'category'  => 'required|string',
        ]);

        $template->update($validated);

        return back()->with('success', 'Template updated successfully.');
    }

    public function approveTemplate(string $id)
    {
        $template = MessageTemplate::findOrFail($id);
        $template->update(['status' => 'approved']);

        return back()->with('success', 'Meta template status updated to Approved.');
    }

    public function setActiveTemplate(Request $request, string $id)
    {
        $template = MessageTemplate::findOrFail($id);
        $type = $request->validate(['type' => 'required|string|in:birthday,anniversary'])['type'];

        // Ensure template is approved before activating
        if ($template->status !== 'approved') {
            return back()->withErrors(['message' => 'Only Meta-approved templates can be chosen as active wishes.']);
        }

        // Deactivate existing active template for this type
        MessageTemplate::where('is_active_for', $type)->update(['is_active_for' => null]);

        // Activate this one
        $template->update(['is_active_for' => $type]);

        return back()->with('success', "Template successfully activated for {$type} wishes.");
    }
}
