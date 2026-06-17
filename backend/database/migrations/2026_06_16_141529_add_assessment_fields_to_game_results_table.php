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
        Schema::table('game_results', function (Blueprint $table) {
         if (!Schema::hasColumn('game_results', 'learning_difficulty_id')) {
            $table->unsignedBigInteger('learning_difficulty_id')->nullable();
        }

        if (!Schema::hasColumn('game_results', 'difficulty_level')) {
            $table->string('difficulty_level')->nullable();
        }

        if (!Schema::hasColumn('game_results', 'correct_count')) {
            $table->integer('correct_count')->nullable();
        }

        if (!Schema::hasColumn('game_results', 'total_questions')) {
            $table->integer('total_questions')->nullable();
        }

        if (!Schema::hasColumn('game_results', 'session_type')) {
            $table->string('session_type')->nullable();
        }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_results', function (Blueprint $table) {
           $table->dropColumn([
            'learning_difficulty_id',
            'difficulty_level',
            'correct_count',
            'total_questions',
            'session_type'
        ]);
        });
    }
};
