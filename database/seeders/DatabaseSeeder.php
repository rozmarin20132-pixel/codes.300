<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Company::factory()
            ->count(20)
            ->has(Employee::factory()->count(random_int(1,10)))
            ->create();
    }
}
