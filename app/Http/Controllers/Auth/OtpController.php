<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Domain\Devotee\Devotee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class OtpController extends Controller
{
    /**
     * Send a 5-digit OTP to the given WhatsApp number.
     * In production this would call a WA Business API gateway.
     * For now, we store it in cache and return success.
     */
    public function send(Request $request)
    {
        $request->validate([
            'whatsapp' => 'required|regex:/^\d{10}$/',
        ]);

        $wa  = $request->whatsapp;
        $otp = str_pad(random_int(10000, 99999), 5, '0', STR_PAD_LEFT);

        // Cache OTP for 10 minutes (keyed by WA number)
        Cache::put("otp:{$wa}", $otp, 600);

        // TODO: dispatch SendOtpJob to WA gateway
        // In demo mode we just return success
        // SendOtpJob::dispatch($wa, $otp);

        return response()->json([
            'success'    => true,
            'expires_at' => now()->addMinutes(10)->toIso8601String(),
            'message'    => 'OTP sent via WhatsApp',
            // DEMO ONLY — remove in production:
            'demo_otp'   => config('app.env') !== 'production' ? $otp : null,
        ]);
    }

    /**
     * Verify OTP. Returns a short-lived devotee_token on success.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'whatsapp' => 'required|regex:/^\d{10}$/',
            'code'     => 'required|digits:5',
        ]);

        $wa      = $request->whatsapp;
        $code    = $request->code;
        $cached  = Cache::get("otp:{$wa}");

        // Demo mode: any 5 digits pass
        $valid = config('app.env') !== 'production'
            ? (strlen($code) === 5)
            : ($cached && $cached === $code);

        if (!$valid) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 422);
        }

        Cache::forget("otp:{$wa}");

        // Mint a short-lived token (stored in cache) for the store step
        $token = Str::random(40);
        Cache::put("otp_token:{$wa}", $token, 300);

        return response()->json(['success' => true, 'devotee_token' => $token]);
    }
}
