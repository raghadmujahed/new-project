<?php

namespace App\Enums;

enum TrainingAssignmentStatus: string
{
    case ASSIGNED = 'assigned';
    case ONGOING = 'ongoing';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match($this) {
            self::ASSIGNED => 'تم التوزيع',
            self::ONGOING => 'قيد التنفيذ',
            self::COMPLETED => 'مكتمل',
        };
    }
}