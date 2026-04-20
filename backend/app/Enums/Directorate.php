<?php

namespace App\Enums;

enum Directorate: string
{
    case CENTRAL = 'وسط';
    case NORTH = 'شمال';
    case SOUTH = 'جنوب';
    case YATTA = 'يطا';

    public function label(): string
    {
        return $this->value;
    }
}