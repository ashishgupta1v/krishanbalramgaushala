<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Devotee\Devotee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UploadController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Upload', ['activeTab' => 'upload']);
    }

    public function downloadSample(): StreamedResponse
    {
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="gaushala_members_sample.csv"'];
        $rows = [
            ['name', 'whatsapp', 'dob', 'anniversary', 'city'],
            ['Amit Sharma',  '9876543211', '1980-04-12', '2005-06-15', 'Ludhiana'],
            ['Geeta Devi',   '9812345679', '1975-08-20', '',           'Ludhiana'],
            ['Rakesh Gupta', '9988776656', '1990-11-03', '2015-02-14', 'Chandigarh'],
        ];

        return response()->stream(function () use ($rows) {
            $fp = fopen('php://output', 'w');
            foreach ($rows as $row) fputcsv($fp, $row);
            fclose($fp);
        }, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate(['rows' => 'required|array|min:1']);

        $imported = 0;
        foreach ($request->rows as $row) {
            if (empty($row['name']) || empty($row['whatsapp'])) continue;
            if (!preg_match('/^\d{10}$/', $row['whatsapp'])) continue;

            Devotee::updateOrCreate(
                ['whatsapp' => $row['whatsapp']],
                [
                    'id'          => (string) Str::uuid(),
                    'name'        => $row['name'],
                    'dob'         => !empty($row['dob']) ? $row['dob'] : null,
                    'anniversary' => !empty($row['anniversary']) ? $row['anniversary'] : null,
                    'city'        => $row['city'] ?? 'Ludhiana',
                    'fb_consent'  => false,
                    'status'      => 'active',
                    'joined_at'   => now(),
                ]
            );
            $imported++;
        }

        return back()->with('success', "{$imported} members imported successfully!");
    }
}
