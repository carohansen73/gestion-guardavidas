<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;


class qrController extends Controller
{
    public function desencriptarQr(Request $request){
        try {
            $validated = $request->validate([
                'encrypted' => 'required|string'
            ]);
            $encrypted = $validated['encrypted'];
            $decrypted = Crypt::decryptString($encrypted);
            $data = json_decode($decrypted);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $request->input('encrypted')
            ], 400);
        }
    }
}
