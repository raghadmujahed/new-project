<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        Department::insert([
            ['name' => 'psychology'],
            ['name' => 'usool_tarbiah'],
            ['name' => 'administration'],
        ]);
    }
}