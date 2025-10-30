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
            ['nombre' => 'Samoa', 'latitud' => -38.860599, 'longitud' => -60.075412, 'qr_encriptado' => '', 'playa_id' => '1'],
            ['nombre' => 'Anexo', 'latitud' => -38.860206, 'longitud' => -60.074211, 'qr_encriptado' => '',  'playa_id' => '1'],
            ['nombre' => 'Nahuel Epu', 'latitud' => -38.859867, 'longitud' => -60.071315,'qr_encriptado' => '', 'playa_id' => '1'],
            ['nombre' => '37', 'latitud' => -38.859512, 'longitud' => -60.067039, 'qr_encriptado' => '',  'playa_id' => '1'],
            ['nombre' => 'Cazadores', 'latitud' => -38.859794, 'longitud' => -60.065235,'qr_encriptado' => '', 'playa_id' => '1'],
            ['nombre' => 'Plumas Verdes', 'latitud' => -38.859539, 'longitud' =>  -60.061256, 'qr_encriptado' => '', 'playa_id' => '1'],
            /* ['nombre' => 'Bagre (Timoneles)', 'latitud' => -38.8640, 'longitud' => -60.0550,  'qr_encriptado' => '', 'playa_id' => '1'],
            ['nombre' => 'Base Claromeco', 'latitud' => -38.8650, 'longitud' => -60.0555,  'qr_encriptado' => '', 'playa_id' => '1'],
            ['nombre' => 'Otro', 'latitud' => -38.8660, 'longitud' => -60.0560,  'qr_encriptado' => '', 'playa_id' => '1'],
            ['nombre' => 'Fuera zona de baño', 'latitud' => -38.8670, 'longitud' => -60.0565, 'qr_encriptado' => '', 'playa_id' => '1'],*/

            // Dunamar
            ['nombre' => 'Kuyem', 'latitud' => -38.861782, 'longitud' => -60.086318, 'qr_encriptado' => '', 'playa_id' => '2'],
            /*['nombre' => 'Boreli', 'latitud' => -38.85800, 'longitud' => -60.0875, 'qr_encriptado' => '', 'playa_id' => '2'],
            ['nombre' => 'Base Dunamar', 'latitud' => -38.85724, 'longitud' => -60.0869, 'qr_encriptado' => '', 'playa_id' => '2'],
            ['nombre' => 'Puesto Foca', 'latitud' => -38.85860, 'longitud' => -60.0885, 'qr_encriptado' => '', 'playa_id' => '2'],
            ['nombre' => 'Otro', 'latitud' => -38.85900, 'longitud' => -60.0890, 'qr_encriptado' => '', 'playa_id' => '2'],
            ['nombre' => 'Fuera zona de baño', 'latitud' => -38.86000, 'longitud' => -60.0900, 'qr_encriptado' => '', 'playa_id' => '2'],*/

            // Orense
            ['nombre' => 'Califa','latitud' => -38.806690, 'longitud' => -59.729438, 'qr_encriptado' => '', 'playa_id' => '3'],
            ['nombre' => 'Refugio', 'latitud' => -38.807643, 'longitud' => -59.731575, 'qr_encriptado' => '', 'playa_id' => '3'],
            ['nombre' => 'Virazon', 'latitud' => -38.808155, 'longitud' => -59.732990, 'qr_encriptado' => '', 'playa_id' => '3'],
            /*['nombre' => 'Piedras Blancas','latitud' => -38.8085241, 'longitud' => -59.7348246, 'qr_encriptado' =>'', 'playa_id' => '3'],
            ['nombre' => 'Otro', 'latitud' => -38.83400, 'longitud' => -59.94900, 'qr_encriptado' => '', 'playa_id' => '3'],
            ['nombre' => 'Fuera zona de baño', 'latitud' => -38.83450, 'longitud' => -59.94950, 'qr_encriptado' => '', 'playa_id' => '3'],*/

            //Reta
            ['nombre' => 'Tequila', 'latitud' => -38.900833, 'longitud' => -60.334167, 'qr_encriptado' => '', 'playa_id' => 4],
            ['nombre' => 'La pausa', 'latitud' => -38.901389, 'longitud' => -60.336389, 'qr_encriptado' => '', 'playa_id' => 4],
            ['nombre' => 'Walters', 'latitud' => -38.902222, 'longitud' => -60.339722, 'qr_encriptado' => '', 'playa_id' => 4],
            ['nombre' => 'Moto', 'latitud' => -38.902222, 'longitud' => -60.340278, 'qr_encriptado' => '', 'playa_id' => 4],
            ['nombre' => 'Rakos', 'latitud' => -38.902778, 'longitud' => -60.344167, 'qr_encriptado' => '', 'playa_id' => 4],
            ['nombre' => 'Cayasta', 'latitud' => -38.900000, 'longitud' => -60.330278, 'qr_encriptado' => '', 'playa_id' => 4],
            /*['nombre' => 'Victor 1', 'latitud' => -38.8995, 'longitud' => -60.3380, 'qr_encriptado' => '', 'playa_id' => 4],
            ['nombre' => 'Victor 2', 'latitud' => -38.9000, 'longitud' => -60.3385, 'qr_encriptado' => '', 'playa_id' => 4],
            ['nombre' => 'Victor 4', 'latitud' => -38.9005, 'longitud' => -60.3390, 'qr_encriptado' => '', 'playa_id' => 4],
            ['nombre' => 'Quincho', 'latitud' => -38.9010, 'longitud' => -60.3393, 'qr_encriptado' => '', 'playa_id' => 4],
            ['nombre' => 'Otro', 'latitud' => -38.9020, 'longitud' => -60.3400, 'qr_encriptado' => '', 'playa_id' => 4],
            ['nombre' => 'Fuera zona de baño', 'latitud' => -38.9025, 'longitud' => -60.3405, 'qr_encriptado' => '', 'playa_id' => 4],*/

            //Tres Arroyos
            ['nombre' => 'Municipalidad', 'latitud' => -38.3767662, 'longitud' => -60.2759348, 'qr_encriptado' => '', 'playa_id' => 5],
            ['nombre' => 'Escuela Nacional N2', 'latitud' => -38.3762244, 'longitud' => -60.2750882, 'qr_encriptado' => '', 'playa_id' => 5],

        ];

        foreach ($puestos as $puesto){
            // Se ejecuta ::created del modelo puesto para generar el QR
            Puesto::create($puesto);
        }
    }
}
