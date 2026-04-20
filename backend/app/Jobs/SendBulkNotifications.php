<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBulkNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userIds;
    protected $type;
    protected $message;
    protected $data;

    public function __construct(array $userIds, string $type, string $message, array $data = [])
    {
        $this->userIds = $userIds;
        $this->type = $type;
        $this->message = $message;
        $this->data = $data;
    }

    public function handle(NotificationService $notificationService): void
    {
        $users = User::whereIn('id', $this->userIds)->get();
        foreach ($users as $user) {
            $notificationService->sendToUser($user, $this->type, $this->message, $this->data);
        }
    }
}