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
    Schema::create('game_results', function (Blueprint $table) {
        $table->id();
        $table->foreignId('child_id')->constrained()->onDelete('cascade'); // ربط بالطفل
        $table->string('game_type'); // نوع اللعبة
        $table->float('raw_score'); // الدرجة الخام اللي الطفل جابها في اللعبة
        $table->float('z_score')->nullable(); // الدرجة المعيارية (السيرفر هيحسبها ويخزنها هنا)
        $table->string('risk_level')->nullable(); // النتيجة (High Risk أو Normal)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_results');
    }
};
