<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Usop',
            'email' => 'usop@example.com',
            'password' => 'password'
        ]);
        User::factory()->create([
            'name' => 'Nami',
            'email' => 'nami@example.com',
            'password' => 'password'
        ]);

        DB::table('roles')->insert([
            'user_id' => 1,
            'roles' => 'admin,user'
        ]);
    }
}
