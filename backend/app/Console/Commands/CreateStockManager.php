<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CreateStockManager extends Command
{
    protected $signature = 'user:stock';
    protected $description = 'Create a user with Stock Manager role';

    public function handle()
    {
        $fname = $this->ask('Enter firstname', 'Stock ');
        $lname = $this->ask('Enter lastname', 'Manager ');
        $email = $this->ask('Enter email', 'stock@inventory.com');
        $password = $this->secret('Enter password (default: password)') ?: 'password';

        // Check if email already exists
        if (User::where('email', $email)->exists()) {
            $this->error("A user with email {$email} already exists.");
            return 1;
        }

        // Create user
        $user = User::create([
            'first_name' => $fname,
            'last_name' => $lname,
            'email' => $email,
            'password' => bcrypt($password),
        ]);

        // Assign role using Spatie
        $user->assignRole('Stock Manager');

        $this->info("Stock Manager user created successfully!");
        $this->info("Name: {$fname} {$lname}");
        $this->info("Email: {$email}");
        $this->info("Password: {$password}");
    }
}
