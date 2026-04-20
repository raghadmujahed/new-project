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
      Schema::create('evaluation_scores', function (Blueprint $table) {
    $table->id();

    $table->foreignId('evaluation_id')
        ->constrained()
        ->onDelete('cascade');

    $table->foreignId('item_id')
        ->constrained('evaluation_items')
        ->onDelete('cascade');
      // نجعل score قابلًا للـ null (لأن النماذج قد لا تحتوي على درجة)
            $table->decimal('score', 5, 2)->nullable()->change();
            
            // إضافة حقول للإجابات النصية والملفات
            $table->text('response_text')->nullable();
            $table->string('file_path')->nullable();

    $table->decimal('score', 5, 2);

    $table->timestamps();

    // 🔥 يمنع تكرار نفس item لنفس evaluation
    $table->unique(['evaluation_id', 'item_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_scores');
    }
};
