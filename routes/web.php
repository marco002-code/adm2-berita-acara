<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MinuteOfEventController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes (Guru & Proktor)
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::post('/attendances', [AttendanceController::class, 'store'])->name('attendances.store');

    Route::get('/minutes', [MinuteOfEventController::class, 'index'])->name('minutes.index');
    Route::post('/minutes', [MinuteOfEventController::class, 'store'])->name('minutes.store');
    Route::get('/minutes/{minute}/print', [MinuteOfEventController::class, 'print'])->name('minutes.print');

    // Admin Panel (Pengaturan Nama Asesmen, Sesi, Ruangan)
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
        Route::post('/rooms', [AdminController::class, 'storeRoom'])->name('rooms.store');
        Route::put('/rooms/{room}', [AdminController::class, 'updateRoom'])->name('rooms.update');
        Route::delete('/rooms/{room}', [AdminController::class, 'destroyRoom'])->name('rooms.destroy');
        Route::post('/sessions', [AdminController::class, 'storeSession'])->name('sessions.store');
        Route::put('/sessions/{session}', [AdminController::class, 'updateSession'])->name('sessions.update');
        Route::delete('/sessions/{session}', [AdminController::class, 'destroySession'])->name('sessions.destroy');
        Route::post('/sessions/generate', [AdminController::class, 'bulkGenerateSessions'])->name('sessions.generate');
        Route::post('/proctors', [AdminController::class, 'storeProctor'])->name('proctors.store');
        Route::put('/proctors/{proctor}', [AdminController::class, 'updateProctor'])->name('proctors.update');
        Route::delete('/proctors/{proctor}', [AdminController::class, 'destroyProctor'])->name('proctors.destroy');
        Route::post('/participants', [AdminController::class, 'storeParticipant'])->name('participants.store');
        Route::put('/participants/{participant}', [AdminController::class, 'updateParticipant'])->name('participants.update');
        Route::delete('/participants/{participant}', [AdminController::class, 'destroyParticipant'])->name('participants.destroy');
    });
});
