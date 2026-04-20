<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Section;

class EnrollmentsSeeder extends Seeder
{
    public function run()
    {
        // جلب أول طالب
        $student = User::whereHas('role', function($q) {
            $q->where('name', 'student');
        })->first();

        // جلب أول شعبة
        $section = Section::first();

        if ($student && $section) {
            Enrollment::firstOrCreate(
                [
                    'user_id' => $student->id,
                    'section_id' => $section->id,
                ],
                [
                    'academic_year' => 2025,
                    'semester' => 'first',
                    'status' => 'active',
                ]
            );
        }
    }
}