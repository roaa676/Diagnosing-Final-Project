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
    Schema::create('game_contents', function (Blueprint $table) {
        $id = $table->id();
        $table->foreignId('learning_difficulty_id')->constrained(); // النوع (قراءة، حساب، إلخ)
        $table->string('level_name'); // مثلاً: المستوى الأول
        $table->integer('difficulty_level'); // 1, 2, 3, 4, 5
        $table->json('content_data'); // هنا بقى الزتونة: الحروف أو الصور أو الأرقام
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_contents');
    }
};
