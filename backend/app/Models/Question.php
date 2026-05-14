<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
protected $fillable = ['learning_difficulty_id', 'question_text', 'order'];

public function difficulty() {
    return $this->belongsTo(LearningDifficulty::class, 'learning_difficulty_id');
}
}
