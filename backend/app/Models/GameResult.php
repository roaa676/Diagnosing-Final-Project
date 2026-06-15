<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameResult extends Model
{
    use HasFactory;
    protected $fillable = ['child_id', 'game_type', 'raw_score', 'z_score', 'risk_level'];

    public function child() {
        return $this->belongsTo(Child::class);
    }
}
