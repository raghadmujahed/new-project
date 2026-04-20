<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeUserStatusRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Helpers\ActivityLogger;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        // $this->authorizeResource(User::class, 'user'); // فعّل لاحقاً
    }

    public function index(Request $request)
    {
        $query = User::with(['role', 'department']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->role_id) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return UserResource::collection($query->paginate($request->per_page ?? 10));
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->createUser($request->validated());

        ActivityLogger::log(
            'user',
            'created',
            'تم إضافة مستخدم جديد',
            $user,
            [
                'email' => $user->email,
                'role_id' => $user->role_id
            ],
            $request->user()
        );

        return new UserResource($user->load(['role', 'department']));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $oldData = $user->getOriginal();
        $user = $this->userService->updateUser($user, $request->validated());

        ActivityLogger::log(
            'user',
            'updated',
            'تم تحديث المستخدم',
            $user,
            [
                'old' => $oldData,
                'new' => $user->getAttributes()
            ],
            $request->user()
        );

        return new UserResource($user->load(['role', 'department']));
    }

    public function destroy(User $user)  // أزلت Request $request لأنه غير مستخدم
    {
        $userName = $user->name;
        $userEmail = $user->email;

        ActivityLogger::log(
            'user',
            'deleted',
            'تم حذف المستخدم',
            $user,
            [
                'deleted_user' => $userName,
                'email' => $userEmail
            ],
            auth()->user()
        );

        $user->delete();

        return response()->json(['message' => 'تم حذف المستخدم']);
    }

    public function changeStatus(ChangeUserStatusRequest $request, User $user)
    {
        $oldStatus = $user->status;
        $user = $this->userService->changeStatus($user, $request->status);

        ActivityLogger::log(
            'user',
            'status_changed',
            'تم تغيير حالة المستخدم',
            $user,
            [
                'old_status' => $oldStatus,
                'new_status' => $user->status
            ],
            $request->user()
        );

        return new UserResource($user->load(['role', 'department']));
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            ActivityLogger::log(
                'auth',
                'login_failed',
                'فشل تسجيل الدخول',
                null,
                [
                    'reason' => 'email_not_found',
                    'email' => $request->email,
                    'ip' => $request->ip()
                ]
            );
            return response()->json(['message' => 'البريد الإلكتروني غير موجود'], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            ActivityLogger::log(
                'auth',
                'login_failed',
                'كلمة المرور خاطئة',
                $user,
                [
                    'reason' => 'wrong_password',
                    'email' => $request->email,
                    'ip' => $request->ip()
                ]
            );
            return response()->json(['message' => 'كلمة المرور غير صحيحة'], 401);
        }

        if ($user->status !== 'active') {
            ActivityLogger::log(
                'auth',
                'login_blocked',
                'حساب غير نشط',
                $user
            );
            return response()->json(['message' => 'الحساب غير نشط'], 403);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        ActivityLogger::log(
            'auth',
            'login',
            'تم تسجيل الدخول',
            $user,
            [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]
        );

        return response()->json([
            'user' => new UserResource($user->load(['role', 'department'])),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            ActivityLogger::log(
                'auth',
                'logout',
                'تم تسجيل الخروج',
                $user,
                [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]
            );
        }

        $user?->currentAccessToken()?->delete();

        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }

    public function bulkAdd(Request $request)
    {
        $request->validate(['users' => 'required|array']);

        $success = [];
        $failed = [];

        foreach ($request->users as $userData) {
            try {
                $user = $this->userService->createUser($userData);
                $success[] = $user;
                ActivityLogger::log(
                    'user',
                    'created_bulk',
                    'تم إضافة مستخدم',
                    $user,
                    [],
                    $request->user()
                );
            } catch (\Exception $e) {
                $failed[] = ['email' => $userData['email'] ?? '?', 'error' => $e->getMessage()];
            }
        }

        ActivityLogger::log(
            'user',
            'bulk_upload',
            'رفع جماعي للمستخدمين',
            null,
            [
                'success_count' => count($success),
                'fail_count' => count($failed)
            ],
            $request->user()
        );

        return response()->json(['success' => $success, 'failed' => $failed]);
    }
}