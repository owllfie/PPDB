<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::updateOrCreate(['id_role' => 1], ['role' => 'Student']);
        Role::updateOrCreate(['id_role' => 2], ['role' => 'Registration Admin']);
        Role::updateOrCreate(['id_role' => 3], ['role' => 'User Admin']);
        Role::updateOrCreate(['id_role' => 4], ['role' => 'Super Admin']);
    }
}
