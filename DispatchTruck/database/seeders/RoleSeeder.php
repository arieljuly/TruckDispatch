<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['role_name' => 'admin', 'description' => 'System administrator'],
            ['role_name' => 'dispatcher', 'description' => 'Manages truck operations'],
            ['role_name' => 'driver', 'description' => 'Handles deliveries'],
            ['role_name' => 'client', 'description' => 'Places delivery requests'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['role_name' => $role['role_name']], // check
                [
                    'description' => $role['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}