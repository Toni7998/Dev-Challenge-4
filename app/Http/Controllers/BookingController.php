<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;

class ReservationController extends Controller
{
    // Comprobar disponibilidad de una fecha
    public function checkAvailability($date)
    {
        $exists = Reservation::where('date', $date)->exists();
        return response()->json(['available' => !$exists]);
    }

    // Reservar una fecha
    public function reserveDate(Request $request)
    {
        $request->validate(['date' => 'required|date|after_or_equal:today']);

        $exists = Reservation::where('date', $request->date)->exists();
        if ($exists) {
            return response()->json(['message' => 'La fecha ya está reservada.'], 400);
        }

        Reservation::create(['date' => $request->date]);
        return response()->json(['message' => 'Reserva realizada correctamente.']);
    }

    // Cancelar reserva
    public function cancelReservation(Request $request)
    {
        $request->validate(['date' => 'required|date']);

        $reservation = Reservation::where('date', $request->date)->first();
        if (!$reservation) {
            return response()->json(['message' => 'No hay reserva para esta fecha.'], 400);
        }

        $reservation->delete();
        return response()->json(['message' => 'Reserva cancelada correctamente.']);
    }
}