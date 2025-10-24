<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeederProduccion extends Seeder
{
    public function run(): void
    {
        $modules = [
            [
                'name' => 'Seguridad',
                'key' => 'security',
                'description' => 'Módulo de gestión de seguridad y accesos'
            ],
            [
                'name' => 'Usuarios',
                'key' => 'users',
                'description' => 'Gestión de usuarios del sistema'
            ],
            [
                'name' => 'Roles',
                'key' => 'roles',
                'description' => 'Gestión de roles y permisos'
            ],
            [
                'name' => 'Modulos',
                'key' => 'modules',
                'description' => 'Gestión de módulos'
            ],
            [
                'name' => 'Permisos',
                'key' => 'permissions',
                'description' => 'Gestión de permisos'
            ],

            
            [
                'name' => 'Departamentos',
                'key' => 'departments',
                'description' => 'Gestión de departamentos'
            ],
            [
                'name' => 'Documentos Tecnicos',
                'key' => 'technicalDocument',
                'description' => 'Gestión de documentos técnicos'
            ],
            [
                'name' => 'Documentos Legales',
                'key' => 'legalDocument',
                'description' => 'Gestión de documentos Legales'
            ],
            [
                'name' => 'Empresa',
                'key' => 'tenders',
                'description' => 'Gestión de empresas'
            ],
            [
                'name' => 'Licitaciones',
                'key' => 'processes',
                'description' => 'Gestión de Licitaciones'
            ],
            [
                'name' => 'Modalidades',
                'key' => 'modality',
                'description' => 'Gestión de modalidades para Doc.'
            ],
         
        
        ];

        foreach ($modules as $module) {
            Module::create($module);
        }
    }
}