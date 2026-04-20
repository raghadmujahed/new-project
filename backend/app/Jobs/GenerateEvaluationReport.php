<?php

namespace App\Jobs;

use App\Models\TrainingAssignment;
use App\Services\EvaluationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateEvaluationReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $trainingAssignmentId;
    protected $format;

    public function __construct(int $trainingAssignmentId, string $format = 'pdf')
    {
        $this->trainingAssignmentId = $trainingAssignmentId;
        $this->format = $format;
    }

    public function handle(EvaluationService $evaluationService): void
    {
        $assignment = TrainingAssignment::find($this->trainingAssignmentId);
        if (!$assignment) return;

        $evaluations = $evaluationService->getStudentEvaluations($assignment->id);
        // توليد التقرير (PDF/Excel) – يمكن استخدام مكتبة مثل Laravel Excel أو DomPDF
        // $content = ...;
        // Storage::put("reports/evaluation_{$assignment->id}.{$this->format}", $content);
    }
}