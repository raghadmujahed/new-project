<?php

namespace App\Enums;

enum TrainingLogStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case APPROVED = 'approved';
    case RETURNED = 'returned';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'مسودة',
            self::SUBMITTED => 'مرسل',
            self::APPROVED => 'معتمد',
            self::RETURNED => 'معاد للطالب',
        };
    }
}