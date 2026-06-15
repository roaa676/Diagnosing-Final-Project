<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AgeNorm;

class AgeNormSeeder extends Seeder
{
    public function run()
    {
        // مصفوفة بكل المعايير العلمية للأعمار والألعاب المختلفة
        $norms = [
            // --- معايير لعبة التمييز البصري (visual_discrimination) ---
            ['age' => 4, 'test_type' => 'visual_discrimination', 'expected_raw_score' => 15, 'standard_deviation' => 2],
            ['age' => 5, 'test_type' => 'visual_discrimination', 'expected_raw_score' => 20, 'standard_deviation' => 2.5],
            ['age' => 6, 'test_type' => 'visual_discrimination', 'expected_raw_score' => 25, 'standard_deviation' => 3],
            ['age' => 7, 'test_type' => 'visual_discrimination', 'expected_raw_score' => 30, 'standard_deviation' => 3.5],

            // --- معايير لعبة الكميات/الحساب (magnitude_comparison) ---
            ['age' => 4, 'test_type' => 'magnitude_comparison', 'expected_raw_score' => 10, 'standard_deviation' => 1.5],
            ['age' => 5, 'test_type' => 'magnitude_comparison', 'expected_raw_score' => 14, 'standard_deviation' => 2],
            ['age' => 6, 'test_type' => 'magnitude_comparison', 'expected_raw_score' => 18, 'standard_deviation' => 2.5],
            ['age' => 7, 'test_type' => 'magnitude_comparison', 'expected_raw_score' => 22, 'standard_deviation' => 3],
        ];

        // إدخال كل البيانات في الداتا بيز
        foreach ($norms as $norm) {
            AgeNorm::create($norm);
        }
    }
}
