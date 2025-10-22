<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Users CRUD
    Volt::route('/users', 'admin.users.index')->name('users');
    Volt::route('/users/create', 'admin.users.create')->name('users.create');
    Volt::route('/users/{userId}/edit', 'admin.users.edit')->name('users.edit');
    
    // Calendars CRUD
    Volt::route('/calendars', 'admin.calendars.index')->name('calendars');
    Volt::route('/calendars/create', 'admin.calendars.create')->name('calendars.create');
    Volt::route('/calendars/{calendarId}/edit', 'admin.calendars.edit')->name('calendars.edit');
    
    // Events CRUD
    Volt::route('/events', 'admin.events.index')->name('events');
    Volt::route('/events/create', 'admin.events.create')->name('events.create');
    Volt::route('/events/{eventId}/edit', 'admin.events.edit')->name('events.edit');
    
    // Invites CRUD
    Volt::route('/invites', 'admin.invites.index')->name('invites');
    Volt::route('/invites/create', 'admin.invites.create')->name('invites.create');
    Volt::route('/invites/{inviteId}/edit', 'admin.invites.edit')->name('invites.edit');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    // Two-Factor Authentication disabled
    // Volt::route('settings/two-factor', 'settings.two-factor')
    //     ->middleware(
    //         when(
    //             Features::canManageTwoFactorAuthentication()
    //                 && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
    //             ['password.confirm'],
    //             [],
    //         ),
    //     )
    //     ->name('two-factor.show');
});
