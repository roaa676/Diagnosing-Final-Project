<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgeNorm extends Model
{
    use HasFactory;
    protected $fillable = ['age', 'test_type', 'expected_raw_score', 'standard_deviation'];
}
