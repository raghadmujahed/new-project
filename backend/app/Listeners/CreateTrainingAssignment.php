<?php

namespace App\Listeners;

use App\Events\TrainingRequestApproved;
use App\Services\TrainingRequestService;

class CreateTrainingAssignment
{
    protected $trainingRequestService;

    public function __construct(TrainingRequestService $trainingRequestService)
    {
        $this->trainingRequestService = $trainingRequestService;
    }

    public function handle(TrainingRequestApproved $event): void
    {
        // سيتم إنشاء TrainingAssignment لكل طالب داخل الخدمة
        // يمكن استدعاء طريقة في الخدمة
    }
}