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
        Schema::table('training_progress', function (Blueprint $table) {
            // إضافة تاريخ آخر جلسة لعب
            $table->date('last_played_date')->nullable()->after('next_level_unlocks_at');
            
            // إضافة عدد الدقائق اللي لعبها في اليوم ده
            $table->integer('daily_time_spent')->default(0)->after('last_played_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_progress', function (Blueprint $table) {
            // حذف العواميد في حالة التراجع
            $table->dropColumn(['last_played_date', 'daily_time_spent']);
        });
    }
};