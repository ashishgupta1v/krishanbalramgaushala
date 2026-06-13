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
    public function create()
    {
        if (session('gaushala_devotee_id')) {
            return redirect()->route('devotee.profile');
        }
        return Inertia::render('Register');
    }

    public function store(Request $request)
    {
        \Log::info('Registration attempt', ['whatsapp' => $request->input('whatsapp'), 'name' => $request->input('name')]);

        // Check if there's a soft-deleted devotee with this whatsapp number
        $existingSoftDeleted = Devotee::withTrashed()
            ->where('whatsapp', $request->input('whatsapp'))
            ->whereNotNull('deleted_at')
            ->first();

        // Check if there's an active (non-deleted) devotee with this number
        $existingActive = Devotee::where('whatsapp', $request->input('whatsapp'))->first();
        if ($existingActive) {
            return response()->json([
                'success' => false,
                'message' => 'This WhatsApp number is already registered. Please Sign In.',
            ], 422);
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:200',
            'whatsapp'    => 'required|string|regex:/^\d{10}$/',
            'dob'         => 'required|date',
            'anniversary' => 'nullable|date',
            'fb_consent'  => 'boolean',
            'password'    => 'required|string|min:6|confirmed',
            'photo'       => 'nullable|image|max:15360',
        ]);

        \Log::info('Validation passed', ['name' => $validated['name'], 'whatsapp' => $validated['whatsapp']]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('devotees', 'public');
        }

        try {
            if ($existingSoftDeleted) {
                // Restore and update the soft-deleted record
                \Log::info('Restoring soft-deleted devotee', ['id' => $existingSoftDeleted->id]);
                $existingSoftDeleted->restore();
                $existingSoftDeleted->update([
                    'name'        => $validated['name'],
                    'whatsapp'    => $validated['whatsapp'],
                    'dob'         => $validated['dob'],
                    'anniversary' => $validated['anniversary'] ?? null,
                    'fb_consent'  => $validated['fb_consent'] ?? false,
                    'status'      => 'active',
                    'joined_at'   => now(),
                    'password'    => Hash::make($validated['password']),
                    'photo_path'  => $photoPath ?? $existingSoftDeleted->photo_path,
                ]);
                $devotee = $existingSoftDeleted->fresh();
            } else {
                // Create new devotee
                $devotee = Devotee::create([
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
            }

            \Log::info('Devotee saved successfully', ['id' => $devotee->id, 'whatsapp' => $devotee->whatsapp]);

            // Verify the devotee was actually persisted
            $check = Devotee::find($devotee->id);
            if (!$check) {
                \Log::error('Devotee NOT found in DB after create!', ['id' => $devotee->id]);
                return response()->json(['success' => false, 'message' => 'Registration failed. Please try again.'], 500);
            }

        } catch (\Exception $e) {
            \Log::error('Devotee creation failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Registration failed. Please try again.'], 500);
        }

        // Dispatch WhatsApp welcome message
        try {
            $welcomeMsg = "🙏 Jai Gau Mata!\n\nDear {name} Ji,\n\nWelcome to our divine Gau Seva family! 🐄\n\nMay Gau Mata bless your bond with eternal love and togetherness.\n\n— Krishan Balram Gaushala, Singla Enclave, Village Dullon Khurd, Pakhowal Road, Ludhiana";
            \App\Jobs\SendWaMessageJob::dispatch($devotee, $welcomeMsg);
            \Log::info('Welcome WhatsApp job dispatched', ['devotee_id' => $devotee->id]);
        } catch (\Exception $e) {
            \Log::error('WhatsApp job dispatch failed', ['error' => $e->getMessage()]);
            // Don't fail registration if WhatsApp dispatch fails
        }

        // Store in session
        session(['gaushala_devotee_id' => $devotee->id]);
        cookie()->queue(cookie()->forever('gaushala_devotee_remember', $devotee->id));

        \Log::info('Registration complete - returning success', ['devotee_id' => $devotee->id]);

        return response()->json(['success' => true, 'devotee' => $devotee]);
    }

    public function showLogin()
    {
        if (session('gaushala_devotee_id')) {
            return redirect()->route('devotee.profile');
        }
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
        cookie()->queue(cookie()->forever('gaushala_devotee_remember', $devotee->id));

        return response()->json(['success' => true, 'devotee' => $devotee]);
    }

    public function logout(Request $request)
    {
        session()->forget('gaushala_devotee_id');
        cookie()->queue(cookie()->forget('gaushala_devotee_remember'));
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
            'photo'       => 'nullable|image|max:15360',
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
