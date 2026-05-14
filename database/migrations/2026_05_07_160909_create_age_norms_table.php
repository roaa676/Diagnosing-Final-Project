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
    Schema::create('age_norms', function (Blueprint $table) {
        $table->id();
        $table->integer('age'); // السن
        $table->string('test_type'); // نوع الاختبار (مثلاً: visual_discrimination)
        $table->float('expected_raw_score'); // المتوسط الطبيعي للسن ده
        $table->float('standard_deviation'); // الانحراف المعياري
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('age_norms');
    }
};
