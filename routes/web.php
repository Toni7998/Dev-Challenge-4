<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;

// Consultar disponibilidad por fecha
Route::get('/availability/{date}', [ReservationController::class, 'checkAvailability'])->name('availability.check');

// Guardar una reserva
Route::post('/reserve', [ReservationController::class, 'store'])->name('reserve');
Route::post('/reserve', [ReservationController::class, 'reserveDate']);

// Obtener todas las reservas
Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
Route::get('/reservations', [ReservationController::class, 'getReservations']);

// Cargar la vista del calendario
Route::get('/calendar', function () {
    return view('calendar');
})->name('calendar');

Route::get('/api/reservations', [ReservationController::class, 'getReservations']);

//  Ruta para cancelar la cita
Route::get('/cancel-reservation/{id}', [ReservationController::class, 'cancel'])->name('reservation.cancel');
