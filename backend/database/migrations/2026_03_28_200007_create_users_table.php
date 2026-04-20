<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // المعرف الجامعي (رقم الطالب أو المعلم)
            $table->string('university_id')->nullable()->unique()->index();

            // المعلومات الأساسية
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();

            // الحالة
            $table->enum('status', ['active', 'inactive', 'suspended'])
                ->default('active');

            // القسم الأكاديمي (علاقة بجدول departments)
            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            // الدور (علاقة بجدول roles)
            $table->foreignId('role_id')
                ->constrained('roles')
                ->cascadeOnDelete();

            // رقم الهاتف
            $table->string('phone')->nullable();

            // التخصص (للطالب) أو المادة (للمعلم) – حقل موحد
            $table->string('major')->nullable();

            // موقع التدريب / مكان العمل (علاقة بجدول training_sites)
            $table->foreignId('training_site_id')
                ->nullable()
                ->constrained('training_sites')
                ->nullOnDelete();

            // توكن تذكر الدخول والحذف الناعم
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();

            // الفهارس الإضافية
            $table->index('status');
            $table->index('department_id');
            $table->index('deleted_at');
            $table->index(['department_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};