<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['id_role' => 1], ['role' => 'user']);
        Role::firstOrCreate(['id_role' => 2], ['role' => 'admin']);
    }
}
