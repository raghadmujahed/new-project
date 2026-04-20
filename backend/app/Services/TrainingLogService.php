<?php

namespace App\Services;

use App\Models\TrainingLog;
use App\Enums\TrainingLogStatus;

class TrainingLogService
{
    public function createLog(array $data, int $studentId): TrainingLog
    {
        $data['status'] = TrainingLogStatus::DRAFT->value;
        return TrainingLog::create($data);
    }

    public function submitLog(TrainingLog $log): TrainingLog
    {
        $log->update(['status' => TrainingLogStatus::SUBMITTED->value]);
        return $log;
    }

    public function reviewLog(TrainingLog $log, string $status, ?string $supervisorNotes = null): TrainingLog
    {
        $log->update([
            'status' => $status,
            'supervisor_notes' => $supervisorNotes ?? $log->supervisor_notes,
        ]);
        return $log;
    }
}