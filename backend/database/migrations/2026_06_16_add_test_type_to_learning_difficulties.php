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
        Schema::table('learning_difficulties', function (Blueprint $table) {
            // أضف حقل test_type لربط الصعوبة بنوع الاختبار في AgeNorms
            $table->string('test_type')->nullable()->after('name_en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('learning_difficulties', function (Blueprint $table) {
            $table->dropColumn('test_type');
        });
    }
};
