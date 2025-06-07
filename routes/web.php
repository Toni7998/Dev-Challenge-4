<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;
use App\Models\Reservation;
use App\Http\Controllers\AvailabilityController;
use App\Models\Availability;

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



Route::get('/cancel-reservation/{uuid}', function ($uuid) {
    \Log::info('Iniciando cancelación de reserva para UUID:', ['uuid' => $uuid]);

    // Buscar la reserva
    $reservation = Reservation::where('uuid', $uuid)->first();

    if (!$reservation) {
        \Log::warning('Reserva no encontrada para el UUID:', ['uuid' => $uuid]);
        return view('reservation_cancelled', [
            'error' => 'No s\'ha trobat la reserva especificada.',
            'uuid' => $uuid,
        ]);
    }

    \Log::info('Reserva encontrada. Procediendo a eliminar.', ['reservation' => $reservation->toArray()]);

    // Eliminar la reserva
    $reservation->delete();

    \Log::info('Reserva eliminada correctamente.', ['uuid' => $uuid]);

    return view('reservation_cancelled', [
        'success' => 'Tu reserva ha sido cancelada correctamente.',
        'uuid' => $uuid,
    ]);
});

// === Backoffice per gestionar disponibilitat ===
Route::get('/reservations', [ReservationController::class, 'index']);
Route::post('/reservations', [ReservationController::class, 'store']);

Route::get('/backoffice', function () {
    return view('backoffice'); // Asegúrate de que tu archivo esté en resources/views/backoffice.blade.php
});

Route::post('/api/reservations', [ReservationController::class, 'store']);
