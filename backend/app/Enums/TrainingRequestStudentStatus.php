<?php

namespace App\Enums;

enum TrainingRequestStudentStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
    case NEEDS_MODIFICATION = 'needs_modification';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'قيد الانتظار',
            self::APPROVED => 'مقبول',
            self::REJECTED => 'مرفوض',
            self::CANCELLED => 'ملغي',
            self::NEEDS_MODIFICATION => 'بحاجة تعديل',
        };
    }
}