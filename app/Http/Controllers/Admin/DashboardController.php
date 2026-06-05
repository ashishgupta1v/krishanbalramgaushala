<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Devotee\Devotee;
use App\Domain\GaushalEvent\GaushalEvent;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $today = now()->format('m-d');

        $stats = [
            'total'               => Devotee::count(),
            'active'              => Devotee::active()->count(),
            'birthdays_today'     => Devotee::active()->birthdayToday()->count(),
            'anniversaries_today' => Devotee::active()->anniversaryToday()->count(),
            'wa_sent_today'       => \App\Domain\Messaging\WishDeliveryLog::whereDate('created_at', now()->toDateString())->where('status', 'sent')->count(),
            'wa_failed_today'     => \App\Domain\Messaging\WishDeliveryLog::whereDate('created_at', now()->toDateString())->where('status', 'failed')->count(),
            'wa_pending_today'    => \App\Domain\Messaging\WishDeliveryLog::whereDate('created_at', now()->toDateString())->where('status', 'pending')->count(),
        ];

        $timeline = $this->buildTimeline();

        $events = GaushalEvent::where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->limit(10)
            ->get()
            ->map(fn ($e) => [
                'id'          => $e->id,
                'title'       => $e->title,
                'description' => $e->description,
                'icon'        => $e->icon,
                'type'        => $e->type,
                'date_label'  => $e->scheduled_at->format('d M Y'),
                'time_label'  => $e->time_label ?? $e->scheduled_at->format('H:i'),
            ]);

        return Inertia::render('Admin/Dashboard', [
            'stats'     => $stats,
            'timeline'  => $timeline,
            'events'    => $events,
            'activeTab' => 'dashboard',
        ]);
    }

    private function buildTimeline(): array
    {
        $hour = (int) now('Asia/Kolkata')->format('H');
        $items = [
            ['time' => '4:00 AM', 'icon' => '🔔', 'title' => 'Morning Bell & Prayers',        'hour' => 4],
            ['time' => '5:30 AM', 'icon' => '🐄', 'title' => 'Cow Feeding — First Round',      'hour' => 5],
            ['time' => '7:00 AM', 'icon' => '📲', 'title' => 'Automated WA Wishes Sent',       'hour' => 7],
            ['time' => '9:00 AM', 'icon' => '🧹', 'title' => 'Gaushala Cleaning & Grooming',   'hour' => 9],
            ['time' => '12:00 PM','icon' => '🥛', 'title' => 'Midday Milk Collection',          'hour' => 12],
            ['time' => '4:00 PM', 'icon' => '🐄', 'title' => 'Cow Feeding — Second Round',     'hour' => 16],
            ['time' => '6:00 PM', 'icon' => '🪔', 'title' => 'Evening Aarti & Blessings',      'hour' => 18],
            ['time' => '8:00 PM', 'icon' => '🌙', 'title' => 'Night Check & Rest',             'hour' => 20],
        ];

        return array_map(function ($item) use ($hour) {
            $item['done'] = $item['hour'] < $hour;
            $item['now']  = $item['hour'] === $hour;
            return $item;
        }, $items);
    }
}
