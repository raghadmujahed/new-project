<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CoursesSeeder extends Seeder
{
    public function run()
    {
        $courses = [
            ['code' => 'EDUC310', 'name' => 'تربية عملية 1', 'credit_hours' => 3, 'type' => 'practical'],
            ['code' => 'EDUC320', 'name' => 'تربية عملية 2', 'credit_hours' => 3, 'type' => 'practical'],
            ['code' => 'PSYC210', 'name' => 'إرشاد نفسي تربوي', 'credit_hours' => 2, 'type' => 'both'],
            ['code' => 'EDUC330', 'name' => 'طرق تدريس', 'credit_hours' => 3, 'type' => 'theoretical'],
        ];

        foreach ($courses as $course) {
            Course::firstOrCreate(['code' => $course['code']], $course);
        }
    }
}