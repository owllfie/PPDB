<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['slug' => 'user.register', 'name' => 'Registration Form'],
            ['slug' => 'admin.dashboard', 'name' => 'Admin Dashboard'],
            ['slug' => 'admin.users', 'name' => 'User Management'],
            ['slug' => 'admin.queue', 'name' => 'Registration Queue'],
            ['slug' => 'admin.reports', 'name' => 'Reports'],
            ['slug' => 'admin.access', 'name' => 'Manage Access Control'],
            ['slug' => 'admin.logs', 'name' => 'Activity Logs'],
            ['slug' => 'admin.settings', 'name' => 'Web Settings'],
            ['slug' => 'admin.backup', 'name' => 'DB Backup'],
        ];

        foreach ($permissions as $p) {
            \App\Models\Permission::updateOrCreate(['slug' => $p['slug']], $p);
        }

        // Map roles to permissions based on current hardcoded logic
        $role4 = \App\Models\Role::find(4); // Super Admin
        if ($role4) {
            $role4->permissions()->sync(\App\Models\Permission::all());
        }

        $role3 = \App\Models\Role::find(3); // User Admin
        if ($role3) {
            $role3->permissions()->sync(\App\Models\Permission::where('slug', 'admin.users')->get());
        }

        $role2 = \App\Models\Role::find(2); // Registration Admin
        if ($role2) {
            $role2->permissions()->sync(\App\Models\Permission::whereIn('slug', ['admin.queue', 'admin.reports'])->get());
        }

        $role1 = \App\Models\Role::find(1); // Student
        if ($role1) {
            $role1->permissions()->sync(\App\Models\Permission::where('slug', 'user.register')->get());
        }
    }
}
