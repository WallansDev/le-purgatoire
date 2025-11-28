<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Intervention;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        Company::factory()
            ->count(5)
            ->has(
                Technician::factory()
                    ->count(8)
                    ->has(Intervention::factory()->count(20))
            )
            ->create();
    }
}
