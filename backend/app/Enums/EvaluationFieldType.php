<?php

namespace App\Enums;

enum EvaluationFieldType: string
{
    case SCORE = 'score';
    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case RADIO = 'radio';
    case CHECKBOX = 'checkbox';
    case DATE = 'date';
    case FILE = 'file';

    public function label(): string
    {
        return match($this) {
            self::SCORE => 'درجة رقمية',
            self::TEXT => 'نص قصير',
            self::TEXTAREA => 'نص طويل',
            self::RADIO => 'اختيار واحد',
            self::CHECKBOX => 'اختيار متعدد',
            self::DATE => 'تاريخ',
            self::FILE => 'رفع ملف',
        };
    }
}