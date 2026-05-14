<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('questions', function (Blueprint $table) {
        $table->id();
        // ربط السؤال بالصعوبة (عسر قراءة، حساب، إلخ)
        $table->foreignId('learning_difficulty_id')->constrained('learning_difficulties')->onDelete('cascade');
        $table->text('question_text'); // نص السؤال
        $table->integer('order')->default(0); // ترتيب السؤال في القائمة
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
