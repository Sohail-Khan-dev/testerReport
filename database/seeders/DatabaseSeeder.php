<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Project;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        User::factory()->create([
            'name' => 'Zubair Khan',
            'email' => 'test@example.com',
            'password' => Hash::make('$2a$12$MMzdXpFc/iiI6e2vB3Y7x.RnPosmBzBrA5bZegPeznHOAb15RZPtm'), // Hash the password
        ]);

        User::factory()->create([
            'name' => 'Khan',
            'email' => 'khan@email.com',
            'password' => Hash::make('$2a$12$MMzdXpFc/iiI6e2vB3Y7x.RnPosmBzBrA5bZegPeznHOAb15RZPtm'),
        ]);

        User::factory()->create([
            'name' => 'Ali',
            'email' => 'ali@email.com',
            'password' => Hash::make('$2a$12$MMzdXpFc/iiI6e2vB3Y7x.RnPosmBzBrA5bZegPeznHOAb15RZPtm'),
        ]);
        Project::factory()->create([
            'name' => 'Un roads'
        ]);
        Project::factory()->create([
            'name' => 'Polio Vaccines'
        ]);
        Project::factory()->create([
            'name' => 'Inj distribution'
        ]);
        
    }
}
