<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
           // Création des rôles pour le guard 'web'
           Role::create(['name' => 'admin', 'guard_name' => 'web']);
           Role::create(['name' => 'vendeur', 'guard_name' => 'web']);
           Role::create(['name' => 'client', 'guard_name' => 'web']);
    }
}
