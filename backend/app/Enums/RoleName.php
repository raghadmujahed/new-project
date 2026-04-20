<?php

namespace App\Enums;

enum RoleName: string
{
    case ADMIN = 'admin';
    case COORDINATOR = 'coordinator';
    case SUPERVISOR = 'supervisor';
    case MENTOR = 'mentor';
    case PRINCIPAL = 'principal';
    case HEALTH_DIRECTORATE = 'health_directorate';
    case EDUCATION_DIRECTORATE = 'education_directorate';
    case STUDENT = 'student';
    case COUNSELOR = 'counselor';
    case PSYCHOLOGIST = 'psychologist';
    case ACADEMIC_SUPERVISOR = 'academic_supervisor';
}