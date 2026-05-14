<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    use HasFactory;

    // تحديد الأعمدة المسموح بتعبئتها (تم إضافة image هنا)
    protected $fillable = ['user_id', 'name', 'age', 'image'];

    // 1. علاقة الطفل بولي الأمر
    public function user()
    {
        // العلاقة هنا: الطفل ينتمي لمستخدم واحد فقط
        return $this->belongsTo(User::class);
    }
    
    // 2. علاقة الطفل بكل الاستبيانات
    public function questionnaires()
    {
        return $this->hasMany(Questionnaire::class);
    }

    // 3. أحدث استبيان فقط (مفيدة جداً للإدارة والسرعة)
    public function latestQuestionnaire()
    {
        return $this->hasOne(Questionnaire::class)->latestOfMany();
    }

    // 4. علاقة الطفل بنتائج الألعاب
    public function gameResults() 
    {
        return $this->hasMany(GameResult::class);
    }
}