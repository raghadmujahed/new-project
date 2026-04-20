<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TrainingSite;

class TrainingSitePolicy
{
    /**
     * تحديد ما إذا كان المستخدم يمكنه إنشاء موقع تدريب.
     */
    public function create(User $user): bool
    {
        // الأدوار المسموح لها بإنشاء مواقع التدريب
        $allowedRoles = ['admin', 'education_directorate', 'ministry_of_health'];
        
        return in_array($user->role?->name, $allowedRoles);
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه عرض أي موقع تدريب.
     */
    public function viewAny(User $user): bool
    {
        return true; // الجميع يمكنهم العرض (أو حسب الصلاحية)
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه عرض موقع تدريب محدد.
     */
    public function view(User $user, TrainingSite $trainingSite): bool
    {
        return true; // الجميع يمكنهم العرض
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه تحديث موقع تدريب.
     */
    public function update(User $user, TrainingSite $trainingSite): bool
    {
        $allowedRoles = ['admin', 'education_directorate', 'ministry_of_health'];
        return in_array($user->role?->name, $allowedRoles);
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه حذف موقع تدريب.
     */
    public function delete(User $user, TrainingSite $trainingSite): bool
    {
        return $user->role?->name === 'admin'; // فقط الأدمن
    }
}