<?php

use App\Http\Controllers\SplashController;
use App\Http\Controllers\DevoteeController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MembersController;
use App\Http\Controllers\Admin\BroadcastController;
use App\Http\Controllers\Admin\FacebookController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\WishController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| GauSeva Connect — Web Routes
|--------------------------------------------------------------------------
*/

// ── PUBLIC ──────────────────────────────────────────────────────────────

// Splash / Choose / App root
Route::get('/', [SplashController::class, 'index'])->name('splash');
Route::get('/choose', [SplashController::class, 'choose'])->name('choose');

// Devotee Registration & Traditional Auth
Route::get('/register', [DevoteeController::class, 'create'])->name('register');
Route::post('/register', [DevoteeController::class, 'store'])->name('register.store');

Route::get('/devotee/login', [DevoteeController::class, 'showLogin'])->name('devotee.login');
Route::post('/devotee/login', [DevoteeController::class, 'login'])->name('devotee.login.post');
Route::post('/devotee/logout', [DevoteeController::class, 'logout'])->name('devotee.logout');

Route::get('/devotee/reset-password', [DevoteeController::class, 'showReset'])->name('devotee.reset-password');
Route::post('/devotee/reset-password', [DevoteeController::class, 'resetPassword'])->name('devotee.reset-password.post');

// Devotee Profile (session-based)
Route::get('/profile', [DevoteeController::class, 'profile'])
    ->middleware('devotee.session')
    ->name('devotee.profile');
Route::put('/profile', [DevoteeController::class, 'updateProfile'])
    ->middleware('devotee.session')
    ->name('devotee.profile.update');

// ── ADMIN AUTH ───────────────────────────────────────────────────────────
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// ── ADMIN PANEL (protected) ──────────────────────────────────────────────
Route::middleware('admin.auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Members
    Route::get('/members', [MembersController::class, 'index'])->name('members');
    Route::get('/members/export', [MembersController::class, 'export'])->name('members.export');
    Route::put('/members/{id}', [MembersController::class, 'update'])->name('members.update');
    Route::delete('/members/{id}', [MembersController::class, 'destroy'])->name('members.destroy');
    Route::post('/members/import', [UploadController::class, 'import'])->name('members.import');

    // Broadcast
    Route::get('/broadcast', [BroadcastController::class, 'index'])->name('broadcast');
    Route::post('/broadcast/send', [BroadcastController::class, 'send'])->name('broadcast.send');
    Route::get('/broadcast/history', [BroadcastController::class, 'history'])->name('broadcast.history');

    // Facebook
    Route::get('/facebook', [FacebookController::class, 'index'])->name('facebook');
    Route::post('/facebook/posts', [FacebookController::class, 'schedulePost'])->name('facebook.schedule');
    Route::patch('/facebook/auto', [FacebookController::class, 'toggleAuto'])->name('facebook.auto');

    // Upload CSV tab
    Route::get('/upload', [UploadController::class, 'index'])->name('upload');
    Route::get('/upload/sample', [UploadController::class, 'downloadSample'])->name('upload.sample');

    // Events CRUD
    Route::get('/events', [EventController::class, 'index'])->name('events');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::put('/events/{id}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{id}', [EventController::class, 'destroy'])->name('events.destroy');

    // Wishes & Templates
    Route::get('/wishes', [WishController::class, 'index'])->name('wishes');
    Route::post('/wishes/templates', [WishController::class, 'submitTemplate'])->name('wishes.templates.submit');
    Route::put('/wishes/templates/{id}', [WishController::class, 'updateTemplate'])->name('wishes.templates.update');
    Route::post('/wishes/templates/{id}/active', [WishController::class, 'setActiveTemplate'])->name('wishes.templates.active');
    Route::post('/wishes/templates/{id}/approve', [WishController::class, 'approveTemplate'])->name('wishes.templates.approve');
});

// ── WEBHOOKS ─────────────────────────────────────────────────────────────
Route::post('/webhook/whatsapp', [\App\Http\Controllers\WebhookController::class, 'whatsapp'])
    ->name('webhook.whatsapp');
