<?php

namespace App\Http\Controllers;

use App\Domain\Devotee\Devotee;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DevoteeController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:200',
            'whatsapp'    => 'required|string|regex:/^\d{10}$/|unique:devotees,whatsapp',
            'dob'         => 'required|date',
            'anniversary' => 'nullable|date',
            'fb_consent'  => 'boolean',
            'password'    => 'required|string|min:6|confirmed',
            'photo'       => 'nullable|image|max:5120',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('devotees', 'public');
        }

        $devotee = Devotee::create([
            'id'          => (string) Str::uuid(),
            'name'        => $validated['name'],
            'whatsapp'    => $validated['whatsapp'],
            'dob'         => $validated['dob'],
            'anniversary' => $validated['anniversary'] ?? null,
            'fb_consent'  => $validated['fb_consent'] ?? false,
            'status'      => 'active',
            'joined_at'   => now(),
            'password'    => Hash::make($validated['password']),
            'photo_path'  => $photoPath,
        ]);

        // Store in session
        session(['gaushala_devotee_id' => $devotee->id]);

        return response()->json(['success' => true, 'devotee' => $devotee]);
    }

    public function showLogin(): Response
    {
        return Inertia::render('Devotee/Login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'whatsapp' => 'required|string|regex:/^\d{10}$/',
            'password' => 'required|string',
        ]);

        $devotee = Devotee::where('whatsapp', $validated['whatsapp'])
            ->where('status', 'active')
            ->first();

        if (!$devotee || !Hash::check($validated['password'], $devotee->password)) {
            return response()->json(['message' => 'Invalid WhatsApp number or password.'], 422);
        }

        session(['gaushala_devotee_id' => $devotee->id]);

        return response()->json(['success' => true, 'devotee' => $devotee]);
    }

    public function logout(Request $request)
    {
        session()->forget('gaushala_devotee_id');
        return redirect()->route('splash');
    }

    public function showReset(): Response
    {
        return Inertia::render('Devotee/ResetPassword');
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'whatsapp' => 'required|string|regex:/^\d{10}$/',
            'name'     => 'required|string|max:200',
            'dob'      => 'required|date',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $devotee = Devotee::where('whatsapp', $validated['whatsapp'])
            ->where('status', 'active')
            ->first();

        if (!$devotee) {
            return response()->json(['message' => 'No devotee found with this WhatsApp number.'], 422);
        }

        // Verify name case-insensitive and DOB
        $nameMatches = strtolower(trim($devotee->name)) === strtolower(trim($validated['name']));
        $dobMatches = $devotee->dob->format('Y-m-d') === date('Y-m-d', strtotime($validated['dob']));

        if (!$nameMatches || !$dobMatches) {
            return response()->json(['message' => 'The provided Name or Date of Birth does not match our records.'], 422);
        }

        $devotee->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json(['success' => true, 'message' => 'Password reset successfully. Please Sign In.']);
    }

    public function profile(Request $request)
    {
        $id = session('gaushala_devotee_id');
        $devotee = $id ? Devotee::find($id) : null;

        if (!$devotee) {
            return redirect()->route('register');
        }

        $events = \App\Domain\GaushalEvent\GaushalEvent::where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->limit(5)
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

        return Inertia::render('Devotee/Profile', [
            'devotee' => $devotee,
            'events'  => $events,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $id = session('gaushala_devotee_id');
        $devotee = $id ? Devotee::find($id) : null;

        if (!$devotee) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:200',
            'dob'         => 'required|date',
            'anniversary' => 'nullable|date',
            'fb_consent'  => 'boolean',
            'photo'       => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            if ($devotee->photo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($devotee->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('devotees', 'public');
        }
        unset($validated['photo']);

        $devotee->update($validated);

        return redirect()->back();
    }
}
