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
            $table->foreignId('child_id')->constrained('children')->onDelete('cascade');
            
            // ضفنا نوع الصعوبة عشان نربط الاستبيان بعسر القراءة أو الحساب
            $table->foreignId('learning_difficulty_id')->constrained('learning_difficulties')->onDelete('cascade'); 
            
            // السطر ده السحري: هيحفظ أي عدد من الإجابات مهما كان (بدل الـ 4 عواميد القديمة)
            $table->json('responses_json')->nullable(); 
            
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