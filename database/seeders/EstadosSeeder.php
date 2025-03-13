<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Estado;


class EstadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = [
            'Aguascalientes', 'Baja California', 'Baja California Sur', 'Campeche', 
            'Chiapas', 'Chihuahua', 'Coahuila', 'Colima', 'Durango', 'Guanajuato', 
            'Guerrero', 'Hidalgo', 'Jalisco', 'Mexico', 'Michoacán', 'Morelos', 
            'Nayarit', 'Nuevo León', 'Oaxaca', 'Puebla', 'Querétaro', 'Quintana Roo', 
            'San Luis Potosí', 'Sinaloa', 'Sonora', 'Tabasco', 'Tamaulipas', 
            'Tlaxcala', 'Veracruz', 'Yucatán', 'Zacatecas'
        ];
    
        foreach ($estados as $estado) {
            Estado::create(['nombre' => $estado]);
        }
    }
}
