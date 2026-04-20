<?php

namespace App\Listeners;

use App\Events\TrainingRequestApproved;
use App\Services\NotificationService;

class SendApprovalNotification
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(TrainingRequestApproved $event): void
    {
        $students = $event->trainingRequest->trainingRequestStudents;
        foreach ($students as $student) {
            $this->notificationService->sendToUser(
                $student->user,
                'training_request_approved',
                'تمت الموافقة على طلب التدريب الخاص بك.'
            );
        }
    }
}