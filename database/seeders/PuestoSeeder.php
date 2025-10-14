<?php

namespace Database\Seeders;

use App\Models\Puesto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PuestoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $puestos = [
            //Claromeco
            ['nombre' => 'Samoa', 'latitud' => -38.8609, 'longitud' => -60.0761, 'qr_encriptado' => '', 'playa_id' => '1'],
            ['nombre' => 'Anexo', 'latitud' => -38.8590, 'longitud' => -60.0525, 'qr_encriptado' => '',  'playa_id' => '1'],
            ['nombre' => 'Nahuel Epu', 'latitud' => -38.8600, 'longitud' => -60.0530,'qr_encriptado' => '', 'playa_id' => '1'],
            ['nombre' => '37', 'latitud' => -38.8610, 'longitud' => -60.0535, 'qr_encriptado' => '',  'playa_id' => '1'],
            ['nombre' => 'Cazadores', 'latitud' => -38.8620, 'longitud' => -60.0540,'qr_encriptado' => '', 'playa_id' => '1'],
            ['nombre' => 'Plumas Verdes', 'latitud' => -38.8630, 'longitud' =>  -60.0545, 'qr_encriptado' => '', 'playa_id' => '1'],
            ['nombre' => 'Bagre (Timoneles)', 'latitud' => -38.8640, 'longitud' => -60.0550,  'qr_encriptado' => '', 'playa_id' => '1'],
            ['nombre' => 'Base Claromeco', 'latitud' => -38.8650, 'longitud' => -60.0555,  'qr_encriptado' => '', 'playa_id' => '1'],
            ['nombre' => 'Otro', 'latitud' => -38.8660, 'longitud' => -60.0560,  'qr_encriptado' => '', 'playa_id' => '1'],
            ['nombre' => 'Fuera zona de baño', 'latitud' => -38.8670, 'longitud' => -60.0565, 'qr_encriptado' => '', 'playa_id' => '1'],

            // Dunamar
            ['nombre' => 'Base Dunamar', 'latitud' => -38.85724, 'longitud' => -60.0869, 'qr_encriptado' => '', 'playa_id' => '2'],
            ['nombre' => 'Kuyen', 'latitud' => -38.85800, 'longitud' => -60.0875, 'qr_encriptado' => '', 'playa_id' => '2'],
            ['nombre' => 'Boreli', 'latitud' => -38.85800, 'longitud' => -60.0875, 'qr_encriptado' => '', 'playa_id' => '2'],
            ['nombre' => 'Puesto Foca', 'latitud' => -38.85860, 'longitud' => -60.0885, 'qr_encriptado' => '', 'playa_id' => '2'],
            ['nombre' => 'Otro', 'latitud' => -38.85900, 'longitud' => -60.0890, 'qr_encriptado' => '', 'playa_id' => '2'],
            ['nombre' => 'Fuera zona de baño', 'latitud' => -38.86000, 'longitud' => -60.0900, 'qr_encriptado' => '', 'playa_id' => '2'],

            // Orense
            ['nombre' => 'Piedras Blancas','latitud' => -38.8085241, 'longitud' => -59.7348246, 'qr_encriptado' =>'', 'playa_id' => '3'],
            ['nombre' => 'Califa','latitud' => -38.8067801, 'longitud' => -59.7299816, 'qr_encriptado' => '', 'playa_id' => '3'],
            ['nombre' => 'Refugio', 'latitud' => -38.83350, 'longitud' => -5994850, 'qr_encriptado' => '', 'playa_id' => '3'],
            ['nombre' => 'Otro', 'latitud' => -38.83400, 'longitud' => -59.94900, 'qr_encriptado' => '', 'playa_id' => '3'],
            ['nombre' => 'Fuera zona de baño', 'latitud' => -38.83450, 'longitud' => -59.94950, 'qr_encriptado' => '', 'playa_id' => '3'],

            //Reta
            ['nombre' => 'Victor 1', 'latitud' => -38.8995, 'longitud' => -60.3380, 'qr_encriptado' => '', 'playa_id' => 4],
            ['nombre' => 'Victor 2', 'latitud' => -38.9000, 'longitud' => -60.3385, 'qr_encriptado' => '', 'playa_id' => 4],
            ['nombre' => 'Victor 4', 'latitud' => -38.9005, 'longitud' => -60.3390, 'qr_encriptado' => '', 'playa_id' => 4],
            ['nombre' => 'Quincho', 'latitud' => -38.9010, 'longitud' => -60.3393, 'qr_encriptado' => '', 'playa_id' => 4],
            ['nombre' => 'Walters', 'latitud' => -38.9017563, 'longitud' => -60.3398567, 'qr_encriptado' => '', 'playa_id' => 4],
            ['nombre' => 'Otro', 'latitud' => -38.9020, 'longitud' => -60.3400, 'qr_encriptado' => '', 'playa_id' => 4],
            ['nombre' => 'Fuera zona de baño', 'latitud' => -38.9025, 'longitud' => -60.3405, 'qr_encriptado' => '', 'playa_id' => 4],

        ];

        foreach ($puestos as $puesto){
            // Se ejecuta ::created del modelo puesto para generar el QR
            Puesto::create($puesto);
        }
    }
}
