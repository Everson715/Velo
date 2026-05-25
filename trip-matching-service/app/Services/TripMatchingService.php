<?php

namespace App\Services;

class TripMatchingService
{
    /**
     * Calcula o valor estimado da viagem.
     */
    public function calculateEstimate(float $distanceKm): array
    {
        // Lógica pura em PHP para calcular preço (R$ 5.00 base + R$ 1.50/km)
        $basePrice = 5.00;
        $pricePerKm = 1.50;
        $totalPrice = $basePrice + ($distanceKm * $pricePerKm);

        return [
            'base_fare' => $basePrice,
            'per_km' => $pricePerKm,
            'distance_km' => $distanceKm,
            'estimated_price' => round($totalPrice, 2)
        ];
    }
}
