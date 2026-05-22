<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
// use App\Models\User;

class DriverController extends Controller
{
    public function vehicle(Request $request)
    {
        // Mock salvar veículo
        return response()->json(['message' => 'Vehicle saved successfully']);
    }

    public function documents(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,png|max:2048'
        ]);

        if ($request->hasFile('document')) {
            // Valida presença do arquivo e salva fake
            $file = $request->file('document');
            $path = $file->storeFake('documents'); // Simulação Storage::fake() ou local
            
            // Simulação de alteração no banco (documentsApproved = true)
            // $user = auth()->user();
            // $user->documentsApproved = true;
            // $user->save();

            return response()->json([
                'message' => 'Document uploaded successfully',
                'documentsApproved' => true,
                'path' => $path
            ], 200);
        }

        return response()->json(['message' => 'No document found'], 400);
    }

    public function toggleStatus(Request $request)
    {
        // Simulação toggle status
        // $user = auth()->user();
        // $user->isOnline = !$user->isOnline;
        // $user->save();

        return response()->json(['message' => 'Status toggled', 'isOnline' => true]);
    }
}
