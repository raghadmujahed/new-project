<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 👇 تشغيل seeders أولًا
        $this->call([
    RoleSeeder::class,
    DepartmentSeeder::class,
    PermissionSeeder::class,
    TrainingSitesSeeder::class,
    RolePermissionSeeder::class,
    SectionsSeeder::class ,  // أضف هذا
    CoursesSeeder::class,
    UsersSeeder::class, // 👈 آخر إشي
    FeatureFlagsSeeder::class , 
    EducationFoundationFormsSeeder::class, 


]);

      
    }
}