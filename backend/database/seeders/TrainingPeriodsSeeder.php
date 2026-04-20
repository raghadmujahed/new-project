<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TrainingPeriod;

class TrainingPeriodsSeeder extends Seeder
{
    public function run()
    {
        $periods = [
            [
                'name' => 'الفصل الأول 2025/2026',
                'start_date' => '2025-09-01',
                'end_date' => '2026-01-15',
                'is_active' => true,
            ],
            [
                'name' => 'الفصل الثاني 2025/2026',
                'start_date' => '2026-02-01',
                'end_date' => '2026-06-15',
                'is_active' => false,
            ],
            [
                'name' => 'الفصل الصيفي 2026',
                'start_date' => '2026-07-01',
                'end_date' => '2026-08-15',
                'is_active' => false,
            ],
        ];

        foreach ($periods as $period) {
            TrainingPeriod::firstOrCreate(['name' => $period['name']], $period);
        }
    }
}