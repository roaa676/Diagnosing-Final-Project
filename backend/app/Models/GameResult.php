<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'game_type',
        'session_type',
        'learning_difficulty_id',
        'difficulty_level',
        'raw_score',
        'correct_count',
        'total_questions',
        'z_score',
        'risk_level',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function learningDifficulty()
    {
        return $this->belongsTo(LearningDifficulty::class);
    }
}
