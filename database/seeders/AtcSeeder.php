<?php

namespace Database\Seeders;

use App\Models\Atc;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AtcSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 ATCs with random company names
        Atc::factory()
            ->count(10)
            ->create();
    }
}
