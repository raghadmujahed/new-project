<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnnouncementRequest;
use App\Http\Requests\UpdateAnnouncementRequest;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class AnnouncementController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Announcement::class, 'announcement');
    }

    /**
     * عرض الإعلانات حسب المستخدم
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Announcement::with('user');

        // منع التكرار كل 5 دقائق
        $cacheKey = 'view_announcements_' . $user->id;

        if (!cache()->has($cacheKey)) {
            ActivityLogger::log(
                'announcement',
                'viewed_list',
                'Visited announcements page',
                null,
                ['ip' => $request->ip()],
                $user
            );
            cache()->put($cacheKey, true, now()->addMinutes(5));
        }

        // فلترة حسب الدور
        if ($user->role?->name !== 'admin') {
            $query->where(function ($q) use ($user) {
                $q->where('target_type', 'all')
                    ->orWhere(function ($q) use ($user) {
                        $q->where('target_type', 'user')
                          ->whereJsonContains('target_ids', $user->id);
                    })
                    ->orWhere(function ($q) use ($user) {
                        $q->where('target_type', 'role')
                          ->whereJsonContains('target_ids', $user->role_id);
                    })
                    ->orWhere(function ($q) use ($user) {
                        $q->where('target_type', 'department')
                          ->whereJsonContains('target_ids', $user->department_id);
                    });
            });
        }

        $announcements = $query->latest()->paginate($request->per_page ?? 15);

        return AnnouncementResource::collection($announcements);
    }

    /**
     * إنشاء إعلان
     */
    public function store(StoreAnnouncementRequest $request)
    {
        $announcement = Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => $request->user()->id,
            'target_type' => $request->target_type ?? 'all',
            'target_ids' => $request->target_ids ?? null,
        ]);

        ActivityLogger::log(
            'announcement',
            'created',
            'Announcement created',
            $announcement,
            [
                'announcement_id' => $announcement->id,
                'title' => $announcement->title,
                'target_type' => $announcement->target_type,
            ],
            $request->user()
        );

        return new AnnouncementResource($announcement->load('user'));
    }

    /**
     * عرض إعلان واحد
     */
    public function show(Announcement $announcement)
    {
        ActivityLogger::log(
            'announcement',
            'viewed',
            'Viewed announcement',
            $announcement,
            ['announcement_id' => $announcement->id],
            auth()->user()
        );

        return new AnnouncementResource($announcement->load('user'));
    }

    /**
     * تحديث إعلان
     */
    public function update(UpdateAnnouncementRequest $request, Announcement $announcement)
    {
        $announcement->update([
            'title' => $request->title ?? $announcement->title,
            'content' => $request->content ?? $announcement->content,
            'target_type' => $request->target_type ?? $announcement->target_type,
            'target_ids' => $request->target_ids ?? $announcement->target_ids,
        ]);

        ActivityLogger::log(
            'announcement',
            'updated',
            'Announcement updated',
            $announcement,
            [
                'announcement_id' => $announcement->id,
                'title' => $announcement->title,
            ],
            $request->user()
        );

        return new AnnouncementResource($announcement);
    }

    /**
     * حذف إعلان
     */
    public function destroy(Announcement $announcement)
    {
        $id = $announcement->id;
        $title = $announcement->title;

        $announcement->delete();

        ActivityLogger::log(
            'announcement',
            'deleted',
            'Announcement deleted',
            null,
            [
                'announcement_id' => $id,
                'title' => $title,
            ],
            auth()->user()
        );

        return response()->json(['message' => 'تم حذف الإعلان']);
    }
}