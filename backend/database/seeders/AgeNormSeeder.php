<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AgeNorm;

class AgeNormSeeder extends Seeder
{
    public function run()
    {
        $norms = [
            ['age' => 4, 'test_type' => 'visual_discrimination', 'expected_raw_score' => 15, 'standard_deviation' => 2],
            ['age' => 5, 'test_type' => 'visual_discrimination', 'expected_raw_score' => 20, 'standard_deviation' => 2.5],
            ['age' => 6, 'test_type' => 'visual_discrimination', 'expected_raw_score' => 25, 'standard_deviation' => 3],
            ['age' => 7, 'test_type' => 'visual_discrimination', 'expected_raw_score' => 30, 'standard_deviation' => 3.5],

            ['age' => 4, 'test_type' => 'magnitude_comparison', 'expected_raw_score' => 10, 'standard_deviation' => 1.5],
            ['age' => 5, 'test_type' => 'magnitude_comparison', 'expected_raw_score' => 14, 'standard_deviation' => 2],
            ['age' => 6, 'test_type' => 'magnitude_comparison', 'expected_raw_score' => 18, 'standard_deviation' => 2.5],
            ['age' => 7, 'test_type' => 'magnitude_comparison', 'expected_raw_score' => 22, 'standard_deviation' => 3],
        ];

        foreach ($norms as $norm) {
            AgeNorm::create($norm);
        }
    }
}
