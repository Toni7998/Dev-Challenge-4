<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Mail\ReservationConfirmation;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller
{
    // Obtener todas las reservas existentes (para mostrarlas en el frontend)
    public function getReservations()
    {
        $reservations = Reservation::all(); // Obtener todas las reservas
        return response()->json($reservations);
    }

    // Comprobar disponibilidad de una fecha
    public function checkAvailability($date)
    {
        $exists = Reservation::where('date', $date)->exists();
        return response()->json(['available' => !$exists]);
    }

    // Reservar una fecha
    public function reserveDate(Request $request)
    {
        \Log::info('Datos recibidos para guardar la reserva:', $request->all());

        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'course' => 'required|string',
            'hour' => 'required|string',
        ]);

        // Verificar si ya existe la reserva para esa fecha y hora
        $exists = Reservation::where('date', $request->date)
            ->where('hour', $request->hour)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'La fecha y hora ya están reservadas.'], 400);
        }

        // Crear la reserva
        $reservation = Reservation::create([
            'date' => $request->date,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'course' => $request->course,
            'hour' => $request->hour,
        ]);

        \Log::info('Reserva guardada correctamente:', $reservation->toArray());

        try {
            // Enviar correo de confirmación
            Mail::to($reservation->email)->send(new ReservationConfirmation($reservation));
            \Log::info('Correo de confirmación enviado correctamente a ' . $reservation->email);
        } catch (\Exception $e) {
            \Log::error('Error al enviar el correo de confirmación: ' . $e->getMessage());
        }

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

    // Obtener las horas disponibles para una fecha
    public function getAvailableHours($date)
    {
        // Definir las horas posibles para la reserva
        $allHours = ['09:00', '10:00', '11:00', '12:00', '13:00'];

        // Obtener las reservas para ese día
        $reservations = Reservation::where('date', $date)->get();

        // Obtener las horas que ya están ocupadas
        $reservedHours = $reservations->pluck('hour')->toArray();

        // Filtrar las horas disponibles
        $availableHours = array_diff($allHours, $reservedHours);

        return response()->json($availableHours);
    }

    // Obtener reservas
    public function index()
    {
        // Supón que tienes un modelo Reservation
        $reservations = \App\Models\Reservation::all();
        return response()->json($reservations);
    }

    // Guardar una nueva reserva
    public function store(Request $request)
    {
        \Log::info('Datos recibidos en store:', $request->all());

        try {
            // Validación de datos
            $validated = $request->validate([
                'date' => 'required|date',
                'course' => 'required|string',
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'required|string|max:15',
            ]);

            \Log::info('Datos validados correctamente:', $validated);

            // Aquí iría la lógica de almacenamiento, por ejemplo:
            // Reservation::create($validated);

            return response()->json(['message' => 'Reserva creada exitosamente'], 200);
        } catch (\Exception $e) {
            \Log::error('Error al guardar la reserva:', ['exception' => $e->getMessage()]);

            return response()->json(['message' => 'Error al procesar la reserva'], 500);
        }
    }
    public function cancel($uuid)
    {
        // Buscar la reserva por UUID
        $reservation = Reservation::where('uuid', $uuid)->first();

        // Verificar si la reserva existe
        if (!$reservation) {
            return view('reservation_cancelled', [
                'error' => 'No s\'ha trobat la reserva especificada.',
            ]);
        }

        // Eliminar la reserva
        $reservation->delete();

        // Retornar una vista de confirmación
        return view('reservation_cancelled', [
            'success' => 'La reserva s\'ha anul·lat correctament.',
        ]);
    }


}
