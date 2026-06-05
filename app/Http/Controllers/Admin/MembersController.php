<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Devotee\Devotee;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;

class MembersController extends Controller
{
    public function index(Request $request): Response
    {
        $milestone = $request->input('milestone', 'all');
        $period = $request->input('period', 'all');

        $members = Devotee::orderByDesc('joined_at')
            ->celebratingMilestone($milestone, $period)
            ->get()
            ->map(fn ($m) => [
                'id'          => $m->id,
                'name'        => $m->name,
                'whatsapp'    => $m->whatsapp,
                'dob'         => $m->dob?->format('Y-m-d'),
                'anniversary' => $m->anniversary?->format('Y-m-d'),
                'city'        => $m->city,
                'fb_consent'  => $m->fb_consent,
                'status'      => $m->status,
                'joined_at'   => $m->joined_at?->format('Y-m-d'),
            ]);

        return Inertia::render('Admin/Members', [
            'members'          => $members,
            'activeTab'        => 'members',
            'currentMilestone' => $milestone,
            'currentPeriod'    => $period,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $devotee = Devotee::findOrFail($id);
        $devotee->update($request->only(['name', 'city', 'status', 'fb_consent']));
        return back()->with('success', 'Member updated.');
    }

    public function destroy(string $id)
    {
        Devotee::findOrFail($id)->delete();
        return back()->with('success', 'Member removed.');
    }

    public function export(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $milestone = $request->input('milestone', 'all');
        $period = $request->input('period', 'all');

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="krishan_balram_devotees_export_' . now()->format('Y-m-d') . '.csv"',
        ];

        $members = Devotee::orderByDesc('joined_at')
            ->celebratingMilestone($milestone, $period)
            ->get();

        return response()->stream(function () use ($members) {
            $fp = fopen('php://output', 'w');
            fputcsv($fp, ['ID', 'Name', 'WhatsApp', 'DOB', 'Anniversary', 'City', 'FB Consent', 'Status', 'Joined At']);

            foreach ($members as $m) {
                fputcsv($fp, [
                    $m->id,
                    $m->name,
                    $m->whatsapp,
                    $m->dob?->format('Y-m-d') ?? '',
                    $m->anniversary?->format('Y-m-d') ?? '',
                    $m->city,
                    $m->fb_consent ? 'Yes' : 'No',
                    $m->status,
                    $m->joined_at?->format('Y-m-d H:i:s') ?? '',
                ]);
            }
            fclose($fp);
        }, 200, $headers);
    }
}
