<?php

namespace App\Enums;

enum SupervisorVisitStatus: string
{
    case SCHEDULED = 'scheduled';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::SCHEDULED => 'مجدد',
            self::COMPLETED => 'منفذة',
            self::CANCELLED => 'ملغية',
        };
    }
}