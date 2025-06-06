<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Availability;

class AvailabilityController extends Controller
{
    public function index()
    {
        // Devuelve todas las disponibilidades
        return response()->json(
            Availability::select('date', 'shift', 'hours')->get()
        );
    }
    

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'shift' => 'required|in:matí,tarda',
            'hours' => 'required|array|min:1',
            'hours.*' => 'string',
        ]);

        $availability = Availability::updateOrCreate(
            ['date' => $request->date, 'shift' => $request->shift],
            ['hours' => json_encode($request->hours)]
        );

        return response()->json($availability, 201);
    }
}
