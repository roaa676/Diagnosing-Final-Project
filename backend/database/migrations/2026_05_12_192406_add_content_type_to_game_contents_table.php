<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('game_contents', function (Blueprint $table) {
            // بنضيف الحقل الجديد ونخليه الديفولت بتاعه تقييم
            $table->enum('content_type', ['assessment', 'training'])->default('assessment')->after('difficulty_level');
        });
    }

    public function down()
    {
        Schema::table('game_contents', function (Blueprint $table) {
            // لو حبينا نرجع في كلامنا بنمسح الحقل
            $table->dropColumn('content_type');
        });
    }
};