<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'csrf_token' => csrf_token(),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],
            'admin' => [
                'authenticated' => session('admin_authenticated', false),
                'username'      => session('admin_username'),
            ],
            'auth' => [
                'devotee' => function () {
                    $id = session('gaushala_devotee_id');
                    if (!$id) return null;
                    
                    $devotee = \App\Domain\Devotee\Devotee::find($id);
                    if (!$devotee) return null;
                    
                    return [
                        'id'              => $devotee->id,
                        'name'            => $devotee->name,
                        'whatsapp'        => $devotee->whatsapp,
                        'photo_url'       => $devotee->photo_url,
                        'avatar_initials' => $devotee->avatar_initials,
                        'city'            => $devotee->city,
                        'dob'             => $devotee->dob?->format('Y-m-d'),
                        'anniversary'     => $devotee->anniversary?->format('Y-m-d'),
                        'fb_consent'      => $devotee->fb_consent,
                    ];
                }
            ],
        ]);
    }
}
