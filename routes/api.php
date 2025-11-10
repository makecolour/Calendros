<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CalendarController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\InviteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Calendar routes
    Route::apiResource('calendars', CalendarController::class);

    // Event routes (nested under calendars for index/store)
    Route::get('/calendars/{calendar}/events', [EventController::class, 'index']);
    Route::post('/calendars/{calendar}/events', [EventController::class, 'store']);
    
    // Event routes (direct for show/update/delete)
    Route::get('/events/{event}', [EventController::class, 'show']);
    Route::put('/events/{event}', [EventController::class, 'update']);
    Route::delete('/events/{event}', [EventController::class, 'destroy']);

    // Invite routes
    Route::get('/invites', [InviteController::class, 'myInvites']);
    Route::get('/events/{event}/invites', [InviteController::class, 'index']);
    Route::post('/events/{event}/invites', [InviteController::class, 'store']);
    Route::put('/invites/{invite}/accept', [InviteController::class, 'accept']);
    Route::put('/invites/{invite}/reject', [InviteController::class, 'reject']);
    Route::delete('/invites/{invite}', [InviteController::class, 'destroy']);
});
