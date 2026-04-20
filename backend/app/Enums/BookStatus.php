<?php

namespace App\Enums;

enum BookStatus: string
{
    case DRAFT = 'draft';
    case SENT_TO_DIRECTORATE = 'sent_to_directorate';
    case DIRECTORATE_APPROVED = 'directorate_approved';
    case SENT_TO_SCHOOL = 'sent_to_school';
    case SCHOOL_APPROVED = 'school_approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'مسودة',
            self::SENT_TO_DIRECTORATE => 'مرسل للمديرية',
            self::DIRECTORATE_APPROVED => 'موافق من المديرية',
            self::SENT_TO_SCHOOL => 'مرسل للمدرسة',
            self::SCHOOL_APPROVED => 'موافق من المدرسة',
            self::REJECTED => 'مرفوض',
        };
    }

    public function isEditableByCoordinator(): bool
    {
        return $this === self::DRAFT || $this === self::REJECTED;
    }
}