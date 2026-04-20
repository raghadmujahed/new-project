<?php

namespace App\Enums;

enum TaskStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case SUBMITTED = 'submitted';
    case GRADED = 'graded';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'قيد الانتظار',
            self::IN_PROGRESS => 'قيد التنفيذ',
            self::COMPLETED => 'مكتمل',
            self::SUBMITTED => 'تم التسليم',
            self::GRADED => 'تم التقييم',
        };
    }
}