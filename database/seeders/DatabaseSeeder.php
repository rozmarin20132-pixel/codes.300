<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->create([
            'name'  => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        $token = $admin->createToken('admin-access-token')->plainTextToken;

        $this->command->info('-------------------------------------------');
        $this->command->warn("Token for admin@example.com:");
        $this->command->line($token);
        $this->command->info('-------------------------------------------');


        $this->call([
            AuthorBookSeeder::class,
        ]);
    }
}
