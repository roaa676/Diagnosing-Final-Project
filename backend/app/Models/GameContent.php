<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameContent extends Model
{
    protected $fillable = [
    'learning_difficulty_id',
    'level_name',
    'difficulty_level',
    'content_type',
    'content_data',
];

public function learningDifficulty()
{
    return $this->belongsTo(LearningDifficulty::class, 'learning_difficulty_id');

}
}
