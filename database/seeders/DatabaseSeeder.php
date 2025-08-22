<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if(!User::whereEmail('admin@inventory.com')->exists()){
            User::create([
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'admin@inventory.com',
                'password' => bcrypt('password'),
            ]);
        }
    }
}
