<?php

namespace App\Events;

use App\Models\TrainingRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TrainingRequestApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public TrainingRequest $trainingRequest;

    public function __construct(TrainingRequest $trainingRequest)
    {
        $this->trainingRequest = $trainingRequest;
    }
}