<?php

namespace App\Enums;

enum EvaluationFormType: string
{
    case EVALUATION = 'evaluation';
    case STUDENT_FORM = 'student_form';

    public function label(): string
    {
        return match($this) {
            self::EVALUATION => 'نموذج تقييم',
            self::STUDENT_FORM => 'نموذج طالب',
        };
    }
}