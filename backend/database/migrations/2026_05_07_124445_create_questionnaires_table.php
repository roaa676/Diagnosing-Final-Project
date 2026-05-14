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
    Schema::create('questionnaires', function (Blueprint $table) {
        $table->id();
        $table->foreignId('child_id')->constrained('children')->onDelete('cascade'); // ربط الاستبيان بالطفل
        
        // إجابات الأسئلة اللي في الواجهة عندك (0 = لا، 1 = أحياناً، 2 = نعم)
        $table->tinyInteger('q1_reading_aloud')->default(0); 
        $table->tinyInteger('q2_confusing_letters')->default(0); 
        $table->tinyInteger('q3_forgetting_instructions')->default(0); 
        $table->tinyInteger('q4_avoiding_reading')->default(0); 
        
        // السكور الكلي عشان نطلع منه مؤشر الخطر المبدئي
        $table->integer('total_risk_score')->default(0); 
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questionnaires');
    }
};
