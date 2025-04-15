<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert(['name' => 'Santiago Admin', 'apellido_paterno' => 'Heras', 'apellido_materno' => 'Gomez', 'numero' => '7775420768', 'email' => 'pryselicitaciones@gmail.com', 'email_verified_at' => '2024-01-17 04:50:32', 'password' => Hash::make('Password'), 'role' => 'Admin']);     
        DB::table('users')->insert(['name' => 'Ricardo', 'apellido_paterno' => 'Perez', 'apellido_materno' => 'Gómez', 'numero' => '7775420768', 'email' => 'ricardo@gmail.com', 'email_verified_at' => '2024-01-17 04:50:32', 'password' => Hash::make('Password'), 'role' => 'Usuario']);

        $user1 = User::where('email', 'pryselicitaciones@gmail.com')->first();
        $user1->assignRole('Admin');
    
        $user4 = User::where('email', 'ricardo@gmail.com')->first();
        $user4->assignRole('Usuario');

        $perfil = Role::where('name', 'Admin')->first();
        $usuarioSistema = Role::where('name', operator: 'Usuario')->first();

       
  
        //todos los permisos a admin
        $perfil->givePermissionTo(Permission::where('module_key', 'modulo')->get());
       
        //Permisos a Usuario-Sistema
        
        $usuarioSistema->givePermissionTo(Permission::where('name', 'documento.index')->first());
        $usuarioSistema->givePermissionTo(Permission::where('name', 'documento.store')->first());
        $usuarioSistema->givePermissionTo(Permission::where('name', 'documento.update')->first());
        $usuarioSistema->givePermissionTo(Permission::where('name', 'documento.delete')->first());

        $usuarioSistema->givePermissionTo(Permission::where('name', 'documentoLegal.index')->first());
        $usuarioSistema->givePermissionTo(Permission::where('name', 'documentoLegal.store')->first());
        $usuarioSistema->givePermissionTo(Permission::where('name', 'documentoLegal.update')->first());
        $usuarioSistema->givePermissionTo(Permission::where('name', 'documentoLegal.delete')->first());
       
        
    }
}
