<?php

namespace App\Enums;

enum WeeklyScheduleDay: string
{
    case SATURDAY = 'saturday';
    case SUNDAY = 'sunday';
    case MONDAY = 'monday';
    case TUESDAY = 'tuesday';
    case WEDNESDAY = 'wednesday';
    case THURSDAY = 'thursday';

    public function label(): string
    {
        return match($this) {
            self::SATURDAY => 'السبت',
            self::SUNDAY => 'الأحد',
            self::MONDAY => 'الإثنين',
            self::TUESDAY => 'الثلاثاء',
            self::WEDNESDAY => 'الأربعاء',
            self::THURSDAY => 'الخميس',
        };
    }

    public function order(): int
    {
        return match($this) {
            self::SATURDAY => 1,
            self::SUNDAY => 2,
            self::MONDAY => 3,
            self::TUESDAY => 4,
            self::WEDNESDAY => 5,
            self::THURSDAY => 6,
        };
    }
}