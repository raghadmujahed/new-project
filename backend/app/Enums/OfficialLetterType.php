<?php

namespace App\Enums;

enum OfficialLetterType: string
{
    case TO_DIRECTORATE = 'to_directorate';
    case TO_SCHOOL = 'to_school';

    public function label(): string
    {
        return match($this) {
            self::TO_DIRECTORATE => 'إلى المديرية',
            self::TO_SCHOOL => 'إلى المدرسة',
        };
    }
}