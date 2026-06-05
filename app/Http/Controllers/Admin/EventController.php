<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\GaushalEvent\GaushalEvent;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index(): Response
    {
        $events = GaushalEvent::orderByDesc('scheduled_at')
            ->get()
            ->map(fn ($e) => [
                'id'           => $e->id,
                'title'        => $e->title,
                'description'  => $e->description,
                'icon'         => $e->icon,
                'type'         => $e->type,
                'scheduled_at' => $e->scheduled_at?->format('Y-m-d\TH:i'),
                'time_label'   => $e->time_label,
                'is_recurring' => $e->is_recurring,
            ]);

        return Inertia::render('Admin/Events', [
            'events'    => $events,
            'activeTab' => 'events',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:200',
            'description'  => 'required|string',
            'icon'         => 'required|string|max:10',
            'type'         => 'required|string|max:50',
            'scheduled_at' => 'required|date',
            'time_label'   => 'nullable|string|max:100',
            'is_recurring' => 'boolean',
        ]);

        GaushalEvent::create([
            'id'           => (string) Str::uuid(),
            'title'        => $validated['title'],
            'description'  => $validated['description'],
            'icon'         => $validated['icon'],
            'type'         => $validated['type'],
            'scheduled_at' => $validated['scheduled_at'],
            'time_label'   => $validated['time_label'] ?? null,
            'is_recurring' => $validated['is_recurring'] ?? false,
        ]);

        return back()->with('success', 'Event created successfully.');
    }

    public function update(Request $request, string $id)
    {
        $event = GaushalEvent::findOrFail($id);

        $validated = $request->validate([
            'title'        => 'required|string|max:200',
            'description'  => 'required|string',
            'icon'         => 'required|string|max:10',
            'type'         => 'required|string|max:50',
            'scheduled_at' => 'required|date',
            'time_label'   => 'nullable|string|max:100',
            'is_recurring' => 'boolean',
        ]);

        $event->update($validated);

        return back()->with('success', 'Event updated successfully.');
    }

    public function destroy(string $id)
    {
        GaushalEvent::findOrFail($id)->delete();
        return back()->with('success', 'Event deleted successfully.');
    }
}
