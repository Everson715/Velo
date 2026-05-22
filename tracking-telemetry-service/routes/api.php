<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('tracking/history/{trip_id}', function ($trip_id, Request $request) {
    // Endpoint simples que leria de um banco NoSQL (Redis/MongoDB)
    $breadcrumbs = [
        ['lat' => -23.550520, 'lng' => -46.633308, 'timestamp' => '2023-10-01T10:00:00Z'],
        ['lat' => -23.551520, 'lng' => -46.634308, 'timestamp' => '2023-10-01T10:01:00Z'],
    ];

    return response()->json([
        'trip_id' => $trip_id,
        'history' => $breadcrumbs
    ]);
});
