<?php

namespace App\Enums;

enum OfficialLetterStatus: string
{
    case DRAFT = 'draft';
    case SENT_TO_DIRECTORATE = 'sent_to_directorate';
    case DIRECTORATE_APPROVED = 'directorate_approved';
    case SENT_TO_SCHOOL = 'sent_to_school';
    case SCHOOL_RECEIVED = 'school_received';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'مسودة',
            self::SENT_TO_DIRECTORATE => 'مرسل للمديرية',
            self::DIRECTORATE_APPROVED => 'موافق من المديرية',
            self::SENT_TO_SCHOOL => 'مرسل للمدرسة',
            self::SCHOOL_RECEIVED => 'مستلم من المدرسة',
            self::COMPLETED => 'مكتمل',
            self::REJECTED => 'مرفوض',
        };
    }
}