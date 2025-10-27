<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;


class QrController extends Controller
{
    public function desencriptarQr(Request $request){
        try {
            $validated = $request->validate([
                'encrypted' => 'required'
            ]);
            // Revertir el urlencode del QR guardado en la BBDD
            $encrypted = urldecode($validated['encrypted']);

            // Desencriptar el QR
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

    public function activeCamera(){
        return view('qr.qr');
    }
}
