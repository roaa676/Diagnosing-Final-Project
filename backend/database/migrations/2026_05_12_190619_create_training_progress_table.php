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
    Schema::create('training_progress', function (Blueprint $table) {
        $table->id();
        $table->foreignId('child_id')->constrained()->onDelete('cascade');
        $table->string('training_type'); // عسر قراءة ولا حساب
        $table->integer('current_level')->default(1); // المستوى الحالي للطفل
        $table->integer('progress_percentage')->default(0); // النسبة المئوية
        $table->timestamp('next_level_unlocks_at')->nullable(); // وقت فتح المستوى القادم
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_progress');
    }
};
