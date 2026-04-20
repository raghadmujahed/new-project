<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EvaluationTemplate;
use App\Models\EvaluationItem;
use App\Models\Department;

class EducationFoundationFormsSeeder extends Seeder
{
    public function run()
    {
        $department = Department::firstOrCreate(['name' => 'أصول التربية']);

        // 1. تقرير الطالب الأسبوعي (نموذج طويل)
        $weekly = EvaluationTemplate::create([
            'name' => 'تقرير الطالب الأسبوعي - أصول التربية',
            'description' => 'تقرير أسبوعي مفصل عن العمل الصفي واللاصفي والتأمل الذاتي',
            'form_type' => 'student_form',
            'department_id' => $department->id,
        ]);
        $weekly->items()->createMany([
            ['title' => 'اسم الطالب', 'field_type' => 'text', 'is_required' => true],
            ['title' => 'المساق', 'field_type' => 'text'],
            ['title' => 'التاريخ (الأسبوع)', 'field_type' => 'text'],
            ['title' => 'الطابور الصباحي (ملاحظات)', 'field_type' => 'textarea'],
            ['title' => 'المناوبة', 'field_type' => 'textarea'],
            ['title' => 'الحصص التي نفذها (كلي، جزئي، أوراق عمل...)', 'field_type' => 'textarea'],
            ['title' => 'الوسائل التي أعدها', 'field_type' => 'textarea'],
            ['title' => 'الأنشطة التي قام بها', 'field_type' => 'textarea'],
            ['title' => 'حضور الاجتماعات', 'field_type' => 'textarea'],
            ['title' => 'جوانب القوة والنجاح', 'field_type' => 'textarea'],
            ['title' => 'جوانب تحتاج للتطوير', 'field_type' => 'textarea'],
            ['title' => 'ما تطلبه من مشرفك', 'field_type' => 'textarea'],
            ['title' => 'الختم المدرسي / التوقيع (صورة)', 'field_type' => 'file'],
        ]);

        // 2. تقرير مدير المدرسة الإداري
        $principal = EvaluationTemplate::create([
            'name' => 'تقرير مدير المدرسة الإداري',
            'description' => 'نموذج رقم (6) تقييم مدير المدرسة',
            'form_type' => 'evaluation',
            'department_id' => $department->id,
        ]);
        $principal->items()->createMany([
            ['title' => 'اسم الطالب', 'field_type' => 'text', 'is_required' => true],
            ['title' => 'المدرسة والمديرية', 'field_type' => 'text'],
            ['title' => 'الدوام (ملاحظات)', 'field_type' => 'textarea'],
            ['title' => 'التعاون مع الهيئة التدريسية', 'field_type' => 'textarea'],
            ['title' => 'التعامل مع الطلبة', 'field_type' => 'textarea'],
            ['title' => 'انخراطه في الأنشطة المدرسية', 'field_type' => 'textarea'],
            ['title' => 'التعامل مع التغذية الراجعة', 'field_type' => 'textarea'],
            ['title' => 'أخلاقيات المهنة', 'field_type' => 'textarea'],
            ['title' => 'ملحوظات عامة', 'field_type' => 'textarea'],
        ]);

        // 3. تقرير الزيارة الصفية (نموذج 6 زيارة صفية)
        $visit = EvaluationTemplate::create([
            'name' => 'تقرير الزيارة الصفية',
            'description' => 'تقرير المشرف الأكاديمي عن زيارة صفية',
            'form_type' => 'evaluation',
            'department_id' => $department->id,
        ]);
        $visit->items()->createMany([
            ['title' => 'اسم الطالب', 'field_type' => 'text', 'is_required' => true],
            ['title' => 'المادة والصف', 'field_type' => 'text'],
            ['title' => 'المحتوى التعليمي (إيجابيات وتطوير)', 'field_type' => 'textarea'],
            ['title' => 'التخطيط للتدريس', 'field_type' => 'textarea'],
            ['title' => 'طرائق التدريس', 'field_type' => 'textarea'],
            ['title' => 'الوسائل التعليمية', 'field_type' => 'textarea'],
            ['title' => 'الإدارة الصفية', 'field_type' => 'textarea'],
            ['title' => 'التقويم', 'field_type' => 'textarea'],
            ['title' => 'أخلاقيات المهنة', 'field_type' => 'textarea'],
            ['title' => 'ملحوظات عامة', 'field_type' => 'textarea'],
        ]);

        // 4. نقد الخبرات
        $critique = EvaluationTemplate::create([
            'name' => 'نقد الخبرات (تحليل درس)',
            'description' => 'تحليل درس مفصل من قبل المشرف',
            'form_type' => 'evaluation',
            'department_id' => $department->id,
        ]);
        $critique->items()->createMany([
            ['title' => 'اسم الطالب', 'field_type' => 'text'],
            ['title' => 'الصف والمادة', 'field_type' => 'text'],
            ['title' => 'الخطط والأهداف', 'field_type' => 'textarea'],
            ['title' => 'التمهيد', 'field_type' => 'textarea'],
            ['title' => 'العرض', 'field_type' => 'textarea'],
            ['title' => 'الخاتمة والغلق', 'field_type' => 'textarea'],
            ['title' => 'إدارة الصف', 'field_type' => 'textarea'],
            ['title' => 'إثارة الدافعية', 'field_type' => 'textarea'],
            ['title' => 'الطرائق والأساليب', 'field_type' => 'textarea'],
            ['title' => 'الوسائل التعليمية', 'field_type' => 'textarea'],
            ['title' => 'التقييم', 'field_type' => 'textarea'],
            ['title' => 'أدوار أخرى للمعلم', 'field_type' => 'textarea'],
        ]);

        $this->command->info('تم إنشاء النماذج النصية الأربعة بنجاح.');
    }
}