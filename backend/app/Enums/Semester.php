<?php

namespace App\Enums;

enum Semester: string
{
    case FIRST = 'first';
    case SECOND = 'second';

    public function label(): string
    {
        return match($this) {
            self::FIRST => 'الفصل الأول',
            self::SECOND => 'الفصل الثاني',
        };
    }
}