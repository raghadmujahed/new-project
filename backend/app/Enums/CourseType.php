<?php

namespace App\Enums;

enum CourseType: string
{
    case PRACTICAL = 'practical';
    case THEORETICAL = 'theoretical';
    case BOTH = 'both';

    public function label(): string
    {
        return match($this) {
            self::PRACTICAL => 'عملي',
            self::THEORETICAL => 'نظري',
            self::BOTH => 'نظري وعملي',
        };
    }
}