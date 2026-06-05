<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class SplashController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Splash');
    }

    public function choose(): Response
    {
        return Inertia::render('Choose');
    }
}
