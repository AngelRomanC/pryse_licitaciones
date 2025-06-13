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
            [
                'nombre' => 'ERI',
                'descripcion' => 'Empresa dedicada a soluciones tecnológicas.',
                'direccion' => 'Avenida Rio Mayo 123, Cuernavaca, Morelos',
                'telefono' => '7772087458',
                'email' => 'eri@pryse.com',
            ],
            [
                'nombre' => 'MAXI',
                'descripcion' => 'Empresa dedicada a soluciones tecnológicas.',
                'direccion' => 'Avenida Rio Mayo 123, Cuernavaca, Morelos',
                'telefono' => '7772189635',
                'email' => 'maxi@pryse.com',
            ],
            [
                'nombre' => 'PROTECCIÓN',
                'descripcion' => 'Empresa dedicada a soluciones tecnológicas.',
                'direccion' => 'Avenida Rio Mayo 123, Cuernavaca, Morelos',
                'telefono' => '7772089935',
                'email' => 'proteccion@pryse.com',
            ],
            [
                'nombre' => 'VALBON',
                'descripcion' => 'Empresa dedicada a soluciones tecnológicas.',
                'direccion' => 'Avenida Rio Mayo 123, Cuernavaca, Morelos',
                'telefono' => '7772009635',
                'email' => 'valbon@pryse.com',
            ],
            [
                'nombre' => 'LAGUNA',
                'descripcion' => 'Empresa dedicada a soluciones tecnológicas.',
                'direccion' => 'Avenida Rio Mayo 123, Cuernavaca, Morelos',
                'telefono' => '7772389635',
                'email' => 'laguna@pryse.com',
            ],

        ]);

        DB::table('modalidads')->insert([
            ['nombre_modalidad' => 'Ninguna'],
            ['nombre_modalidad' => 'I. Seguridad privada a personas'],
            ['nombre_modalidad' => 'II. Seguridad privada en los bienes'],
            ['nombre_modalidad' => 'III. Seguridad privada en el traslado de bienes o valores '],
            ['nombre_modalidad' => 'IV. Servicios de alarmas y de monitoreo electrónico '],

        ]);

    }
}
