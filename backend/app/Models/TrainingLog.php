<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingLog extends Model
{
    use HasFactory;

    // ده بيسمح للـ Controller إنه يسجل الـ child_id في الجدول ده
    protected $fillable = ['child_id']; 
}