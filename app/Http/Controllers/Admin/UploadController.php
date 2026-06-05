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

        $errors = [];
        $validatedRows = [];

        foreach ($request->rows as $index => $row) {
            $rowNum = $index + 1;
            
            $name = !empty($row['name']) ? trim($row['name']) : null;
            if (empty($name)) {
                $errors[] = "Row {$rowNum}: The 'name' field is required.";
                continue;
            }

            if (empty($row['whatsapp'])) {
                $errors[] = "Row {$rowNum} (Name: {$name}): The 'whatsapp' field is required.";
                continue;
            }

            $whatsapp = preg_replace('/\D/', '', $row['whatsapp']);
            if (strlen($whatsapp) !== 10) {
                $errors[] = "Row {$rowNum} (Name: {$name}): WhatsApp number must be exactly 10 digits.";
                continue;
            }

            $dob = null;
            if (!empty($row['dob'])) {
                $dob = $this->parseAndNormalizeDate($row['dob']);
                if (!$dob) {
                    $errors[] = "Row {$rowNum} (Name: {$name}): Date of Birth '{$row['dob']}' is invalid or not in an accepted format (use DD-MM-YYYY or YYYY-MM-DD).";
                }
            }

            $anniversary = null;
            if (!empty($row['anniversary'])) {
                $anniversary = $this->parseAndNormalizeDate($row['anniversary']);
                if (!$anniversary) {
                    $errors[] = "Row {$rowNum} (Name: {$name}): Anniversary Date '{$row['anniversary']}' is invalid or not in an accepted format (use DD-MM-YYYY or YYYY-MM-DD).";
                }
            }

            if (empty($errors)) {
                $validatedRows[] = [
                    'name'        => $name,
                    'whatsapp'    => $whatsapp,
                    'dob'         => $dob,
                    'anniversary' => $anniversary,
                    'city'        => !empty($row['city']) ? trim($row['city']) : 'Ludhiana',
                ];
            }
        }

        if (!empty($errors)) {
            $displayErrors = array_slice($errors, 0, 15);
            if (count($errors) > 15) {
                $displayErrors[] = "... and " . (count($errors) - 15) . " more errors.";
            }
            throw \Illuminate\Validation\ValidationException::withMessages([
                'rows' => $displayErrors
            ]);
        }

        $imported = 0;
        \Illuminate\Support\Facades\DB::transaction(function () use ($validatedRows, &$imported) {
            foreach ($validatedRows as $data) {
                Devotee::updateOrCreate(
                    ['whatsapp' => $data['whatsapp']],
                    [
                        'id'          => (string) Str::uuid(),
                        'name'        => $data['name'],
                        'dob'         => $data['dob'],
                        'anniversary' => $data['anniversary'],
                        'city'        => $data['city'],
                        'fb_consent'  => false,
                        'status'      => 'active',
                        'joined_at'   => now(),
                    ]
                );
                $imported++;
            }
        });

        return back()->with('success', "{$imported} members imported successfully!");
    }

    /**
     * Normalize custom date inputs (DD-MM-YYYY, DD/MM/YYYY, etc.) to YYYY-MM-DD.
     */
    private function parseAndNormalizeDate(?string $dateStr): ?string
    {
        if (empty($dateStr)) {
            return null;
        }

        $dateStr = trim($dateStr);
        $formats = [
            'Y-m-d',
            'd-m-Y',
            'd/m/Y',
            'Y/m/d',
            'd.m.Y',
            'Y.m.d',
        ];

        foreach ($formats as $format) {
            try {
                $d = \Carbon\Carbon::createFromFormat($format, $dateStr);
                if ($d && $d->format($format) === $dateStr) {
                    return $d->format('Y-m-d');
                }
            } catch (\Exception $e) {
                // Ignore exception and try next format
            }
        }

        // Standard PHP strtotime fallback
        try {
            $time = strtotime($dateStr);
            if ($time !== false) {
                return date('Y-m-d', $time);
            }
        } catch (\Exception $e) {
            // Ignore
        }

        return null;
    }
}
