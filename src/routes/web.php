<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard.calendar');
    }

    return redirect()->route('login');
});

Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::redirect('/', '/dashboard/calendar')->name('index');

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::get('/calendar/reservations', [ReservationController::class, 'index'])->name('calendar.reservations.index');
    Route::post('/calendar/reservations', [ReservationController::class, 'store'])->name('calendar.reservations.store');
    Route::get('/calendar/reservations/{reservation}', [ReservationController::class, 'show'])->name('calendar.reservations.show');
    Route::patch('/calendar/reservations/{reservation}', [ReservationController::class, 'update'])->name('calendar.reservations.update');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::patch('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::patch('/settings', [SettingController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
