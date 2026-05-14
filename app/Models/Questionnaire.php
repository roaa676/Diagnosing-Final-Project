<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Questionnaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'q1_reading_aloud',
        'q2_confusing_letters',
        'q3_forgetting_instructions',
        'q4_avoiding_reading',
        'total_risk_score',
        'risk_level',
    ];

    // 👇 ضيف الدالة دي هنا 👇
    public function child()
    {
        return $this->belongsTo(Child::class);
    }
}
