<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('game_results', function (Blueprint $table) {
            $table->string('session_type')->default('assessment')->after('game_type');
            $table->foreignId('learning_difficulty_id')->nullable()->after('session_type')
                ->constrained('learning_difficulties')->nullOnDelete();
            $table->unsignedTinyInteger('difficulty_level')->nullable()->after('learning_difficulty_id');
            $table->unsignedSmallInteger('correct_count')->nullable()->after('raw_score');
            $table->unsignedSmallInteger('total_questions')->nullable()->after('correct_count');
        });
    }

    public function down(): void
    {
        Schema::table('game_results', function (Blueprint $table) {
            $table->dropConstrainedForeignId('learning_difficulty_id');
            $table->dropColumn(['session_type', 'difficulty_level', 'correct_count', 'total_questions']);
        });
    }
};
