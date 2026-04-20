<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EvaluationTemplate;
use App\Models\EvaluationItem;

class EvaluationTemplateSeeder extends Seeder
{
    public function run()
    {
        // قالب تقييم أداء طالب تدريب عملي (بدون قسم)
        $template1 = EvaluationTemplate::create([
            'name' => 'تقييم أداء الطالب في التدريب الميداني',
            'description' => 'نموذج تقييم شامل من قبل المعلم أو المشرف',
            'form_type' => 'evaluation',
            'department_id' => null,
        ]);
        
        $items1 = [
            ['title' => 'الالتزام بالمواعيد', 'field_type' => 'score', 'max_score' => 10, 'is_required' => true, 'weight' => 10],
            ['title' => 'جودة العمل المنجز', 'field_type' => 'score', 'max_score' => 20, 'is_required' => true, 'weight' => 25],
            ['title' => 'التواصل مع الفريق', 'field_type' => 'radio', 'options' => ['ممتاز', 'جيد', 'ضعيف'], 'is_required' => true, 'weight' => 15],
            ['title' => 'ملاحظات إضافية', 'field_type' => 'textarea', 'is_required' => false, 'weight' => null],
        ];
        
        foreach ($items1 as $item) {
            $template1->items()->create($item);
        }
        
        // قالب استبيان طالب (نموذج استمارة)
        $template2 = EvaluationTemplate::create([
            'name' => 'استبيان رأي الطالب عن موقع التدريب',
            'description' => 'يقوم الطالب بتعبئته بعد انتهاء التدريب',
            'form_type' => 'student_form',
            'department_id' => null,
        ]);
        
        $items2 = [
            ['title' => 'مدى استفادتك من التدريب', 'field_type' => 'radio', 'options' => ['كبيرة', 'متوسطة', 'قليلة'], 'is_required' => true],
            ['title' => 'هل توصي بهذا الموقع لزملائك؟', 'field_type' => 'radio', 'options' => ['نعم', 'لا'], 'is_required' => true],
            ['title' => 'اقتراحاتك للتطوير', 'field_type' => 'textarea', 'is_required' => false],
        ];
        
        foreach ($items2 as $item) {
            $template2->items()->create($item);
        }
    }
}