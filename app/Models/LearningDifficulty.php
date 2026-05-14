<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningDifficulty extends Model
{
    protected $fillable = ['name_ar', 'name_en', 'description', 'symptoms', 'parent_advice', 'icon'];

    // السطر ده مهم جداً عشان يحول الـ JSON لـ Array أوتوماتيك
    protected $casts = [
        'symptoms' => 'array',
    ];
    public function questions() {
    return $this->hasMany(Question::class);
}
}