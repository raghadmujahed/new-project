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

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->authorizeResource(User::class, 'user');
    }

    // ================= USERS =================

    public function index(Request $request)
    {
        $query = User::with(['role', 'department']);

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('major')) {
            $query->where('major', 'like', '%' . $request->major . '%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return response()->json($query->paginate(10));
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->createUser($request->validated());

        activity('user')
            ->causedBy($request->user())
            ->performedOn($user)
            ->event('created')
            ->withProperties([
                'email' => $user->email,
                'role_id' => $user->role_id
            ])
            ->log('تم إضافة مستخدم جديد');

        return new UserResource($user->load(['role', 'department']));
    }

    public function show(User $user)
    {
        return new UserResource($user->load(['role', 'department']));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $oldData = $user->getOriginal();

        $user = $this->userService->updateUser($user, $request->validated());

        activity('user')
            ->causedBy($request->user())
            ->performedOn($user)
            ->event('updated')
            ->withProperties([
                'old' => $oldData,
                'new' => $user->getAttributes()
            ])
            ->log('تم تحديث المستخدم');

        return new UserResource($user->load(['role', 'department']));
    }

    public function destroy(Request $request, User $user)
    {
        activity('user')
            ->causedBy($request->user())
            ->event('deleted')
            ->withProperties([
                'deleted_user' => $user->name,
                'email' => $user->email
            ])
            ->log('تم حذف المستخدم');

        $user->delete();

        return response()->json(['message' => 'تم حذف المستخدم']);
    }

    public function changeStatus(ChangeUserStatusRequest $request, User $user)
    {
        $oldStatus = $user->status;

        $user = $this->userService->changeStatus($user, $request->status);

        activity('user')
            ->causedBy($request->user())
            ->performedOn($user)
            ->event('status_changed')
            ->withProperties([
                'old_status' => $oldStatus,
                'new_status' => $user->status
            ])
            ->log('تم تغيير حالة المستخدم');

        return new UserResource($user->load(['role', 'department']));
    }

    // ================= AUTH =================

    public function login(LoginRequest $request)
    {
        $email = $request->email;
        $password = $request->password;

        // 1. تحقق من الحقول
        if (!$email) {
            return response()->json(['message' => 'البريد الإلكتروني مطلوب'], 422);
        }

        if (!$password) {
            return response()->json(['message' => 'كلمة المرور مطلوبة'], 422);
        }

        // 2. البحث عن المستخدم
        $user = User::where('email', $email)->first();

        if (!$user) {
            activity('auth')
                ->event('login_failed')
                ->withProperties([
                    'reason' => 'email_not_found',
                    'email' => $email,
                    'ip' => $request->ip()
                ])
                ->log('فشل تسجيل الدخول');

            return response()->json(['message' => 'البريد الإلكتروني غير موجود'], 404);
        }

        // 3. كلمة المرور
        if (!Hash::check($password, $user->password)) {
            activity('auth')
                ->causedBy($user)
                ->event('login_failed')
                ->withProperties([
                    'reason' => 'wrong_password',
                    'email' => $email,
                    'ip' => $request->ip()
                ])
                ->log('كلمة المرور خاطئة');

            return response()->json(['message' => 'كلمة المرور غير صحيحة'], 401);
        }

        // 4. الحساب غير مفعل
        if ($user->status !== 'active') {
            activity('auth')
                ->causedBy($user)
                ->event('login_blocked')
                ->log('حساب غير نشط');

            return response()->json(['message' => 'الحساب غير نشط'], 403);
        }

        // 5. نجاح تسجيل الدخول
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        activity('auth')
            ->causedBy($user)
            ->event('login')
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ])
            ->log('تم تسجيل الدخول');

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
            activity('auth')
                ->causedBy($user)
                ->event('logout')
                ->withProperties([
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ])
                ->log('تم تسجيل الخروج');
        }

        $user?->currentAccessToken()?->delete();

        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }

    public function currentUser(Request $request)
    {
        return new UserResource($request->user()->load(['role', 'department']));
    }

    // ================= BULK =================

    public function bulkAdd(Request $request)
    {
        $request->validate(['users' => 'required|array']);

        $success = [];
        $failed = [];

        foreach ($request->users as $userData) {
            try {
                $user = $this->userService->createUser($userData);
                $success[] = $user;

                activity('user')
                    ->causedBy($request->user())
                    ->performedOn($user)
                    ->event('created_bulk')
                    ->log('تم إضافة مستخدم');

            } catch (\Exception $e) {
                $failed[] = [
                    'email' => $userData['email'] ?? '?',
                    'error' => $e->getMessage()
                ];
            }
        }

        activity('user')
            ->causedBy($request->user())
            ->event('bulk_upload')
            ->withProperties([
                'success_count' => count($success),
                'fail_count' => count($failed)
            ])
            ->log('رفع جماعي للمستخدمين');

        return response()->json([
            'success' => $success,
            'failed' => $failed
        ]);
    }
}