<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermisosSeederProduccion extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

       $permissions = [
            // USERS
            ['name' => 'users.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'users'],
            ['name' => 'users.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'users'],
            ['name' => 'users.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'users'],
            ['name' => 'users.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'users'],

            // MODULES
            ['name' => 'module.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'modules'],
            ['name' => 'module.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'modules'],
            ['name' => 'module.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'modules'],
            ['name' => 'module.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'modules'],

            // PERMISSIONS
            ['name' => 'permissions.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'permissions'],
            ['name' => 'permissions.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'permissions'],
            ['name' => 'permissions.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'permissions'],
            ['name' => 'permissions.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'permissions'],

            // ROLES
            ['name' => 'roles.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'roles'],
            ['name' => 'roles.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'roles'],
            ['name' => 'roles.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'roles'],
            ['name' => 'roles.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'roles'],

            // DEPARTAMENTOS
            ['name' => 'departamento.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'departments'],
            ['name' => 'departamento.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'departments'],
            ['name' => 'departamento.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'departments'],
            ['name' => 'departamento.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'departments'],

            // DOCUMENTOS TÉCNICOS
            ['name' => 'documento.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'technicalDocument'],
            ['name' => 'documento.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'technicalDocument'],
            ['name' => 'documento.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'technicalDocument'],
            ['name' => 'documento.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'technicalDocument'],

            // DOCUMENTOS LEGALES
            ['name' => 'documentoLegal.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'legalDocument'],
            ['name' => 'documentoLegal.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'legalDocument'],
            ['name' => 'documentoLegal.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'legalDocument'],
            ['name' => 'documentoLegal.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'legalDocument'],

            // EMPRESAS
            ['name' => 'empresa.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'tenders'],
            ['name' => 'empresa.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'tenders'],
            ['name' => 'empresa.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'tenders'],
            ['name' => 'empresa.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'tenders'],

            // LICITACIONES
            ['name' => 'licitacion.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'processes'],
            ['name' => 'licitacion.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'processes'],
            ['name' => 'licitacion.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'processes'],
            ['name' => 'licitacion.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'processes'],

            // MODALIDAD
            ['name' => 'modalidad.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'modality'],
            ['name' => 'modalidad.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'modality'],
            ['name' => 'modalidad.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'modality'],
            ['name' => 'modalidad.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'modality'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']], // Buscar por nombre
                [
                    'guard_name' => $permission['guard_name'],
                    'description' => $permission['description'],
                    'module_key' => $permission['module_key'],
                ]
            );
        }
    }
}
