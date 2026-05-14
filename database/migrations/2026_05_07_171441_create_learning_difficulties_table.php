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
    Schema::create('learning_difficulties', function (Blueprint $table) {
        $table->id();
        $table->string('name_ar'); // اسم الصعوبة (عسر القراءة)
        $table->string('name_en'); // (Dyslexia)
        $table->text('description'); // ما هي؟ (الشرح اللي في الصورة)
        $table->json('symptoms'); // الأعراض الشائعة (هنخزنها كـ قائمة)
        $table->text('parent_advice'); // دليل الأهل (النصيحة اللي في الصورة)
        $table->string('icon')->nullable(); // اسم الأيقونة لو الفرونت محتاجها
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_difficulties');
    }
};
