<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                
        Permission::create(['name' => 'usuarios.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'cat']);
        Permission::create(['name' => 'usuarios.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'cat']);
        Permission::create(['name' => 'usuarios.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'cat']);
        Permission::create(['name' => 'usuarios.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'cat']);
          
        Permission::create(['name' => 'documento.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'cat']);
        Permission::create(['name' => 'documento.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'cat']);
        Permission::create(['name' => 'documento.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'cat']);
        Permission::create(['name' => 'documento.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'cat']);
      
        Permission::create(['name' => 'documentoLegal.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'cat']);
        Permission::create(['name' => 'documentoLegal.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'cat']);
        Permission::create(['name' => 'documentoLegal.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'cat']);
        Permission::create(['name' => 'documentoLegal.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'cat']);
      

    }
}
