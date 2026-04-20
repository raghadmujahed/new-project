<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class NotificationController extends Controller
{
    /**
     * عرض الإشعارات
     */
    public function index(Request $request)
    {
        ActivityLogger::log(
            'notification',
            'viewed_list',
            'Opened notifications page',
            null,
            [],
            $request->user()
        );

        $notifications = Notification::where('user_id', $request->user()->id)
            ->latest()
            ->paginate($request->per_page ?? 15);

        ActivityLogger::log(
            'notification',
            'viewed_data',
            'Fetched notifications list',
            null,
            [
                'user_id' => $request->user()->id,
                'count' => $notifications->count()
            ],
            $request->user()
        );

        return NotificationResource::collection($notifications);
    }

    /**
     * عدد غير المقروء
     */
    public function unreadCount(Request $request)
    {
        $count = Notification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->count();

        ActivityLogger::log(
            'notification',
            'checked',
            'Checked unread notifications count',
            null,
            [
                'user_id' => $request->user()->id,
                'unread_count' => $count
            ],
            $request->user()
        );

        return response()->json(['unread_count' => $count]);
    }

    /**
     * تعليم إشعار كمقروء
     */
    public function markAsRead(Notification $notification)
    {
        $this->authorize('update', $notification);

        $notification->update([
            'read_at' => now()
        ]);

        ActivityLogger::log(
            'notification',
            'updated',
            'Marked notification as read',
            $notification,
            ['notification_id' => $notification->id],
            auth()->user()
        );

        return new NotificationResource($notification);
    }

    /**
     * تعليم الكل كمقروء
     */
    public function markAllAsRead(Request $request)
    {
        $userId = $request->user()->id;

        $updated = Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        ActivityLogger::log(
            'notification',
            'updated',
            'Marked all notifications as read',
            null,
            [
                'user_id' => $userId,
                'updated_count' => $updated
            ],
            $request->user()
        );

        return response()->json([
            'message' => 'تم تحديث جميع الإشعارات كمقروءة'
        ]);
    }

    /**
     * حذف إشعار
     */
    public function destroy(Notification $notification)
    {
        $this->authorize('delete', $notification);

        $notificationId = $notification->id;

        $notification->delete();

        ActivityLogger::log(
            'notification',
            'deleted',
            'Deleted notification',
            null,
            ['notification_id' => $notificationId],
            auth()->user()
        );

        return response()->json([
            'message' => 'تم حذف الإشعار'
        ]);
    }
}