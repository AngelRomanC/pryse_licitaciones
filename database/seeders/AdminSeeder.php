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
        DB::table('users')->insert(['name' => 'Santiago', 'apellido_paterno' => 'Heras', 'apellido_materno' => 'Gomez', 'numero' => '7775420768', 'email' => 'admin@gmail.com', 'email_verified_at' => '2024-01-17 04:50:32', 'password' => Hash::make('Password'), 'role' => 'Admin']);
        DB::table('users')->insert(['name' => 'Miguel', 'apellido_paterno' => 'roman', 'apellido_materno' => 'Chano', 'numero' => '7772152738', 'email' => 'rcmo202029@gmail.com', 'email_verified_at' => '2024-01-17 04:50:32', 'password' => Hash::make('Password'), 'role' => 'Alumno']);
        DB::table('users')->insert(['name' => 'José Santiago', 'apellido_paterno' => 'Heras', 'apellido_materno' => 'Gómez', 'numero' => '7775420768', 'email' => 'santiheras09@gmail.com', 'email_verified_at' => '2024-01-17 04:50:32', 'password' => Hash::make('Password'), 'role' => 'Alumno']);
        DB::table('users')->insert(['name' => 'Ricardo', 'apellido_paterno' => 'Perez', 'apellido_materno' => 'Gómez', 'numero' => '7775420768', 'email' => 'ricardo@gmail.com', 'email_verified_at' => '2024-01-17 04:50:32', 'password' => Hash::make('Password'), 'role' => 'Usuario']);

        $user1 = User::where('email', 'admin@gmail.com')->first();
        $user1->assignRole('Admin');
        $user2 = User::where('email', 'rcmo202029@gmail.com')->first();
        $user2->assignRole('Alumno');
        $user3 = User::where('email', 'santiheras09@gmail.com')->first();
        $user3->assignRole('Alumno');

        $user4 = User::where('email', 'ricardo@gmail.com')->first();
        $user4->assignRole('Usuario');

        $perfil = Role::where('name', 'Admin')->first();
        $user = Role::where('name', operator: 'Alumno')->first();
        $usuarioSistema = Role::where('name', operator: 'Usuario')->first();

        DB::table('alumnos')->insert(['cuatrimestre' => '10', 'matricula' => 'RCMO20032', 'user_id' => '2']);
        DB::table('alumnos')->insert(['cuatrimestre' => '11', 'matricula' => 'HGJO200332', 'user_id' => '3']);
  
        //todos los permisos a admin
        $perfil->givePermissionTo(Permission::where('module_key', 'modulo')->get());
        $user->givePermissionTo(Permission::where('name', 'academico.update')->get());
        //$perfil->givePermissionTo(Permission::where('module_key', 'cat')->get());


        //probando los permisos para alumno  
   ;

        $user->givePermissionTo(Permission::where('name', 'documento.index')->first());
        $user->givePermissionTo(Permission::where('name', 'documento.store')->first());
        $user->givePermissionTo(Permission::where('name', 'documento.update')->first());
        $user->givePermissionTo(Permission::where('name', 'documento.delete')->first());

        $user->givePermissionTo(Permission::where('name', 'documentoLegal.index')->first());
        $user->givePermissionTo(Permission::where('name', 'documentoLegal.store')->first());
        $user->givePermissionTo(Permission::where('name', 'documentoLegal.update')->first());
        $user->givePermissionTo(Permission::where('name', 'documentoLegal.delete')->first());

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
