<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Departamento;


class DatosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('departamentos')->insert([
            ['nombre_departamento' => 'Normatividad'],
            ['nombre_departamento' => 'Proyecto'],
            ['nombre_departamento' => 'Administración'],
            ['nombre_departamento' => 'Contabilidad'],
            ['nombre_departamento' => 'Legal'],
        ]);

        DB::table('tipo_de_documentos')->insert([
            ['nombre_documento' => 'Autorización Aguascalientes'],
            ['nombre_documento' => 'Autorización Baja California Sur'],
            ['nombre_documento' => 'Autorización Campeche'],
            ['nombre_documento' => 'Autorización CDMX'],
            ['nombre_documento' => 'Acta Constitutiva'],
            ['nombre_documento' => 'Identificación Oficial'],
        ]);

        DB::table('empresas')->insert([
            [
                'nombre' => 'PRYSE',
                'descripcion' => 'Empresa dedicada a soluciones tecnológicas.',
                'direccion' => 'Avenida Rio Mayo 123, Cuernavaca, Morelos',
                'telefono' => '1234567890',
                'email' => 'pryse@pryse.com',
            ],

        ]);

        DB::table('modalidads')->insert([
            ['nombre_modalidad' => 'Seguridad privada a personas'],
            ['nombre_modalidad' => 'Seguridad privada en los bienes'],
            ['nombre_modalidad' => 'Seguridad privada en el traslado de bienes o valores '],
            ['nombre_modalidad' => 'Servicios de alarmas y de monitoreo electrónico '],

        ]);

    }
}
