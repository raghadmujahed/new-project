<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Role;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role?->name === 'admin';
    }

    public function rules(): array
    {
        $rules = [
            'university_id'   => 'required|string|max:255|unique:users',
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users',
            'password'        => 'required|string|min:8',
            'status'          => 'required|in:active,inactive,suspended',
            'role_id'         => 'required|exists:roles,id',
            'phone'           => 'nullable|string|max:20',
            'department_id'   => 'nullable|exists:departments,id',
            'training_site_id'=> 'nullable|exists:training_sites,id',
            'major'           => 'nullable|string|max:255',
        ];

        $roleId = $this->input('role_id');
        $role = Role::find($roleId);
        $roleName = $role ? $role->name : null;

        switch ($roleName) {
            case 'student':
                $rules['major']         = 'required|string|max:255';
                $rules['department_id'] = 'required|exists:departments,id';
                break;

            case 'teacher':
                $rules['major']               = 'required|string|max:255';  // المادة التي يدرسها
                $rules['training_site_id']    = 'required|exists:training_sites,id';
                break;

            case 'school_manager':
            case 'counselor':
            case 'psychologist':
                $rules['training_site_id']    = 'required|exists:training_sites,id';
                break;

            case 'academic_supervisor':
                $rules['department_id']       = 'required|exists:departments,id';
                break;

            default:
                // لا قواعد إضافية
                break;
        }

        return $rules;
    }
}