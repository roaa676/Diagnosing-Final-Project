<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningDifficulty extends Model
{
    protected $fillable = ['name_ar', 'name_en', 'test_type', 'description', 'symptoms', 'parent_advice', 'icon'];

    // السطر ده مهم جداً عشان يحول الـ JSON لـ Array أوتوماتيك
    protected $casts = [
        'symptoms' => 'array',
    ];
    public function questions() {
    return $this->hasMany(Question::class);
}
}