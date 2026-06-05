<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Automation\FbPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class FacebookController extends Controller
{
    public function index(): Response
    {
        $posts = FbPost::orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(fn ($p) => [
                'id'           => $p->id,
                'type'         => $p->type,
                'content'      => $p->content,
                'devotee_count'=> $p->devotee_count,
                'status'       => $p->status,
                'when'         => $p->scheduled_at?->format('d M Y H:i') ?? '—',
            ]);

        return Inertia::render('Admin/Facebook', [
            'posts'     => $posts,
            'autoOn'    => (bool) cache('fb_auto_enabled', true),
            'activeTab' => 'facebook',
        ]);
    }

    public function schedulePost(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'date'    => 'nullable|date',
            'time'    => 'nullable|string',
        ]);

        $scheduledAt = $request->date
            ? \Carbon\Carbon::parse($request->date . ' ' . ($request->time ?? '07:00'))
            : now()->setTimeFromTimeString($request->time ?? '07:00');

        FbPost::create([
            'id'           => (string) Str::uuid(),
            'type'         => 'manual',
            'content'      => $request->content,
            'devotee_count'=> 0,
            'status'       => 'scheduled',
            'scheduled_at' => $scheduledAt,
        ]);

        return back()->with('success', 'Post scheduled!');
    }

    public function toggleAuto(Request $request)
    {
        $enabled = (bool) $request->enabled;
        cache(['fb_auto_enabled' => $enabled], 86400 * 365);
        return response()->json(['autoOn' => $enabled]);
    }
}
