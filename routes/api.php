<?php

use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AvailabilityController;

// Obtener todas las reservas
Route::get('reservations', [ReservationController::class, 'getReservations']);

// Realizar una nueva reserva
Route::post('reserve', [ReservationController::class, 'reserveDate']);

// Consultar disponibilidad
Route::get('available-hours/{date}', [ReservationController::class, 'getAvailableHours']);

// Cancelar una reserva
Route::delete('cancel', [ReservationController::class, 'cancelReservation']);

Route::post('/availabilities', [AvailabilityController::class, 'store']);
Route::get('/availabilities', [AvailabilityController::class, 'index']);