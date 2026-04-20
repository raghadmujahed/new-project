<?php

namespace App\Enums;

enum GoverningBody: string
{
    case DIRECTORATE_OF_EDUCATION = 'directorate_of_education';
    case MINISTRY_OF_HEALTH = 'ministry_of_health';

    public function label(): string
    {
        return match($this) {
            self::DIRECTORATE_OF_EDUCATION => 'مديرية التربية',
            self::MINISTRY_OF_HEALTH => 'وزارة الصحة',
        };
    }
}