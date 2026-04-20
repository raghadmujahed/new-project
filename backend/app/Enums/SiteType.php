<?php

namespace App\Enums;

enum SiteType: string
{
    case SCHOOL = 'school';
    case HEALTH_CENTER = 'health_center';

    public function label(): string
    {
        return match($this) {
            self::SCHOOL => 'مدرسة',
            self::HEALTH_CENTER => 'مركز صحي',
        };
    }
}