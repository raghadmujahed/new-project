<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    TrainingRequestController,
    TrainingAssignmentController,
    EvaluationController,
    UserController,
    AttendanceController,
    TaskController,
    TrainingLogController,
    OfficialLetterController,
    ConversationController,
    AnnouncementController,
    DashboardController,
    TrainingSiteController,
    CourseController,
    SectionController,
    EnrollmentController,
    DepartmentController,
    RoleController,
    PermissionController,
    StudentPortfolioController,
    SupervisorVisitController,
    BackupController,
    ActivityLogController,
    TrainingPeriodController,
    EvaluationTemplateController,
    NotificationController,
    NoteController,
    WeeklyScheduleController,
    FeatureFlagController,
    PortfolioEntryController,
    TaskSubmissionController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/login', [UserController::class, 'login'])->name('login');

// Protected routes (require authentication)
Route::middleware(['auth:sanctum'])->group(function () {

    // ========== AUTH & DASHBOARD ==========
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/user', [UserController::class, 'currentUser']);

    // ========== USERS, ROLES, DEPARTMENTS ==========
    Route::apiResource('users', UserController::class);
    Route::patch('users/{user}/status', [UserController::class, 'changeStatus']);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('permissions', PermissionController::class);
    Route::apiResource('departments', DepartmentController::class);

    // ========== COURSES, SECTIONS, ENROLLMENTS ==========
    Route::apiResource('courses', CourseController::class);
    Route::apiResource('sections', SectionController::class);
    Route::apiResource('enrollments', EnrollmentController::class);
    Route::get('/backups/{id}', [BackupController::class, 'show']);
    Route::get('/backups/{id}/table/{tableName}', [BackupController::class, 'getTableData']);

    // ========== TRAINING SITES & PERIODS ==========
    Route::apiResource('training-sites', TrainingSiteController::class);
    Route::apiResource('training-periods', TrainingPeriodController::class);
    Route::patch('training-periods/{training_period}/set-active', [TrainingPeriodController::class, 'setActive']);
    Route::get('/training-periods/active', [TrainingPeriodController::class, 'getActive']);
    Route::apiResource('backups', BackupController::class);
    Route::get('backups/{backup}', [BackupController::class, 'show']);

    // ========== TRAINING REQUESTS ==========
    Route::apiResource('training-requests', TrainingRequestController::class);
    Route::post('training-requests/{training_request}/send-to-directorate', [TrainingRequestController::class, 'sendToDirectorate']);
    Route::post('training-requests/{training_request}/directorate-approve', [TrainingRequestController::class, 'directorateApprove']);
    Route::post('training-requests/{training_request}/send-to-school', [TrainingRequestController::class, 'sendToSchool']);
    Route::post('training-requests/{training_request}/school-approve', [TrainingRequestController::class, 'schoolApprove']);
    Route::post('training-requests/{training_request}/reject', [TrainingRequestController::class, 'reject']);

    // ========== TRAINING ASSIGNMENTS ==========
    Route::apiResource('training-assignments', TrainingAssignmentController::class);

    // ========== ATTENDANCE ==========
    Route::apiResource('attendances', AttendanceController::class);
    Route::patch('attendances/{attendance}/approve', [AttendanceController::class, 'approve']);
    Route::get('attendance-summary', [AttendanceController::class, 'summary']);

    // ========== TRAINING LOGS ==========
    Route::apiResource('training-logs', TrainingLogController::class);
    Route::post('training-logs/{training_log}/submit', [TrainingLogController::class, 'submit']);
    Route::post('training-logs/{training_log}/review', [TrainingLogController::class, 'review']);

    // ========== TASKS & SUBMISSIONS ==========
    Route::apiResource('tasks', TaskController::class);
    Route::post('tasks/{task}/submit', [TaskController::class, 'submit']);
    Route::post('task-submissions/{submission}/grade', [TaskController::class, 'grade']);

    // ========== EVALUATIONS & TEMPLATES ==========
    Route::apiResource('evaluations', EvaluationController::class);
    Route::apiResource('evaluation-templates', EvaluationTemplateController::class);
    Route::post('evaluation-templates/{evaluation_template}/items', [EvaluationTemplateController::class, 'addItem']);
    Route::put('evaluation-items/{item}', [EvaluationTemplateController::class, 'updateItem']);
    Route::delete('evaluation-items/{item}', [EvaluationTemplateController::class, 'deleteItem']);

    // ========== PORTFOLIOS ==========
    Route::apiResource('student-portfolios', StudentPortfolioController::class);
    Route::post('student-portfolios/{student_portfolio}/entries', [StudentPortfolioController::class, 'addEntry']);
    Route::put('portfolio-entries/{entry}', [StudentPortfolioController::class, 'updateEntry']);
    Route::delete('portfolio-entries/{entry}', [StudentPortfolioController::class, 'deleteEntry']);
    Route::get('/my-portfolio', [StudentPortfolioController::class, 'getMyPortfolio']);

    // ========== SUPERVISOR VISITS ==========
    Route::apiResource('supervisor-visits', SupervisorVisitController::class);
    Route::post('supervisor-visits/{supervisor_visit}/complete', [SupervisorVisitController::class, 'complete']);

    // ========== OFFICIAL LETTERS ==========
    Route::apiResource('official-letters', OfficialLetterController::class);
    Route::post('official-letters/{official_letter}/send', [OfficialLetterController::class, 'send']);
    Route::post('official-letters/{official_letter}/receive', [OfficialLetterController::class, 'receive']);
    Route::post('official-letters/{official_letter}/approve', [OfficialLetterController::class, 'approve']);

    // ========== MESSAGES ==========
    Route::apiResource('conversations', ConversationController::class);
    Route::post('conversations/{conversation}/messages', [ConversationController::class, 'sendMessage']);

    // ========== ANNOUNCEMENTS ==========
    Route::apiResource('announcements', AnnouncementController::class);

    // ========== NOTIFICATIONS ==========
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::patch('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy']);

    // ========== NOTES ==========
    Route::apiResource('notes', NoteController::class);

    // ========== WEEKLY SCHEDULES ==========
    Route::apiResource('weekly-schedules', WeeklyScheduleController::class);

    // ========== FEATURE FLAGS ==========
    Route::get('feature-flags', [FeatureFlagController::class, 'index']);
    Route::patch('feature-flags/{feature_flag}', [FeatureFlagController::class, 'update']);
    Route::get('feature-flags/check/{name}', [FeatureFlagController::class, 'check']);

    // ========== BACKUPS ==========
    Route::apiResource('backups', BackupController::class);
    Route::post('backups/{backup}/restore', [BackupController::class, 'restore']);

    // ========== ACTIVITY LOGS ==========
    Route::apiResource('activity-logs', ActivityLogController::class);

    // ========== STUDENT SPECIFIC ROUTES ==========
    Route::prefix('student')->group(function () {
        Route::get('/training-requests', [TrainingRequestController::class, 'studentIndex']);
        Route::post('/training-requests', [TrainingRequestController::class, 'studentStore']);
        Route::get('/schedule', [WeeklyScheduleController::class, 'studentSchedule']);

        Route::get('/training-logs', [TrainingLogController::class, 'getTrainingLogs']);
        Route::post('/training-logs', [TrainingLogController::class, 'store']);
        Route::put('/training-logs/{training_log}', [TrainingLogController::class, 'update']);
        Route::post('/training-logs/{training_log}/submit', [TrainingLogController::class, 'submit']);

        Route::get('/portfolio', [StudentPortfolioController::class, 'show']);
        Route::post('/portfolio/entries', [PortfolioEntryController::class, 'store']);
        Route::put('/portfolio/entries/{entry}', [PortfolioEntryController::class, 'update']);
        Route::delete('/portfolio/entries/{entry}', [PortfolioEntryController::class, 'destroy']);

        Route::get('/tasks', [TaskController::class, 'studentIndex']);
        Route::post('/tasks/{task}/submit', [TaskSubmissionController::class, 'store']);

        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    });
});