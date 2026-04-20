<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();

            // 🔹 بيانات الإعلان الأساسية
            $table->string('title');
            $table->text('content');

            // 🔹 منشئ الإعلان
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            /**
             * 🔥 نظام الاستهداف (Targeting System)
             */

            // نوع الاستهداف:
            // all = الجميع
            // role = أدوار
            // user = مستخدمين محددين
            // department = أقسام
            $table->enum('target_type', ['all', 'role', 'user', 'department'])
                ->default('all');

            // تخزين IDs بشكل JSON (مرن جدًا)
            // مثال: [1,2,3]
            $table->json('target_ids')->nullable();

            /**
             * اختياري لتحسين النظام لاحقاً
             */

            // أولوية الإعلان (لو بدك لاحقاً)
            $table->unsignedTinyInteger('priority')->default(0);

            // هل هو مهم / pinned
            $table->boolean('is_pinned')->default(false);

            $table->timestamps();

            // 🔹 Indexes لتحسين الأداء
            $table->index('user_id');
            $table->index('target_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};