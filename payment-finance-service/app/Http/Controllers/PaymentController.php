<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function authorizePayment(Request $request)
    {
        // Simula bloqueio de saldo/cartão do passageiro
        $transactionId = 'txn_' . bin2hex(random_bytes(8));
        return response()->json([
            'status' => 'AUTHORIZED', 
            'transaction_id' => $transactionId
        ]);
    }

    public function capturePayment(Request $request)
    {
        // Efetiva a cobrança
        return response()->json([
            'status' => 'SUCCESS', 
            'message' => 'Payment captured successfully'
        ]);
    }

    public function balance(Request $request)
    {
        // Pega saldo do motorista
        // Na prática a auth_user_id seria injetada pelo JWTAuthMiddleware
        $driverId = request('auth_user_id', 'driver-uuid-mock');
        
        return response()->json([
            'driver_id' => $driverId,
            'balance' => 154.50,
            'currency' => 'BRL'
        ]);
    }
}
