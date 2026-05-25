<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TripMatchingService;

class TripController extends Controller
{
    protected $matchingService;

    public function __construct(TripMatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    public function estimate(Request $request)
    {
        $request->validate(['distance_km' => 'required|numeric|min:0']);
        $estimate = $this->matchingService->calculateEstimate($request->distance_km);
        
        return response()->json($estimate);
    }

    public function requestTrip(Request $request)
    {
        // Cria corrida com status SEARCHING
        return response()->json(['message' => 'Trip requested', 'status' => 'SEARCHING'], 201);
    }

    public function available(Request $request)
    {
        // Retorna corridas em status SEARCHING
        return response()->json(['data' => []]);
    }

    public function accept($id, Request $request)
    {
        // Associa auth_user_id como driverId e muda status para MATCHED
        $driverId = $request->input('auth_user_id');
        return response()->json(['message' => 'Trip matched', 'trip_id' => $id, 'driver_id' => $driverId]);
    }

    public function arrive($id)
    {
        return response()->json(['message' => 'Driver arrived', 'status' => 'ARRIVED']);
    }

    public function start($id)
    {
        return response()->json(['message' => 'Trip started', 'status' => 'IN_PROGRESS']);
    }

    public function complete($id)
    {
        return response()->json(['message' => 'Trip completed', 'status' => 'COMPLETED']);
    }

    public function cancel($id)
    {
        return response()->json(['message' => 'Trip cancelled', 'status' => 'CANCELLED']);
    }
}
