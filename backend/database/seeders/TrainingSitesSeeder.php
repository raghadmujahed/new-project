<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TrainingSite;

class TrainingSitesSeeder extends Seeder
{
    public function run()
    {
        $schools = [
            ['name' => 'مدرسة ذكور الخليل الأساسية', 'location' => 'الخليل - وسط', 'phone' => '02-1111111', 'directorate' => 'وسط', 'is_active' => true, 'capacity' => 10],
            ['name' => 'مدرسة إناث الخليل الثانوية', 'location' => 'الخليل - وسط', 'phone' => '02-1111112', 'directorate' => 'وسط', 'is_active' => true, 'capacity' => 8],
            ['name' => 'مدرسة الخليل الأساسية المختلطة', 'location' => 'الخليل - وسط', 'phone' => '02-1111113', 'directorate' => 'وسط', 'is_active' => true, 'capacity' => 12],

            ['name' => 'مدرسة حلحول الثانوية للبنين', 'location' => 'حلحول', 'phone' => '02-2222221', 'directorate' => 'شمال', 'is_active' => true, 'capacity' => 10],
            ['name' => 'مدرسة بنات حلحول الأساسية', 'location' => 'حلحول', 'phone' => '02-2222222', 'directorate' => 'شمال', 'is_active' => true, 'capacity' => 8],
            ['name' => 'مدرسة سعير الثانوية', 'location' => 'سعير', 'phone' => '02-2222223', 'directorate' => 'شمال', 'is_active' => true, 'capacity' => 12],

            ['name' => 'مدرسة دورا الثانوية للبنين', 'location' => 'دورا', 'phone' => '02-3333331', 'directorate' => 'جنوب', 'is_active' => true, 'capacity' => 10],
            ['name' => 'مدرسة بنات دورا الأساسية', 'location' => 'دورا', 'phone' => '02-3333332', 'directorate' => 'جنوب', 'is_active' => true, 'capacity' => 9],

            ['name' => 'مدرسة يطا الثانوية', 'location' => 'يطا', 'phone' => '02-4444441', 'directorate' => 'يطا', 'is_active' => true, 'capacity' => 15],
            ['name' => 'مدرسة بنات يطا الأساسية', 'location' => 'يطا', 'phone' => '02-4444442', 'directorate' => 'يطا', 'is_active' => true, 'capacity' => 10],
        ];

        foreach ($schools as $school) {
            $school['governing_body'] = 'directorate_of_education';
            TrainingSite::create($school);
        }
    }
}