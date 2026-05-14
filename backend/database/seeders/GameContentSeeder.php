<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameContentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('game_contents')->truncate(); // مسح القديم

        $contents = [
            // ==============================================================
            // 🔴 أولاً: أسئلة التشخيص (Assessment) - للمرة الأولى فقط
            // ==============================================================
            
            // 1. تشخيص عسر القراءة - المستوى 1 (15 سؤال - تمييز الحروف)
            [
                'learning_difficulty_id' => 1,
                'level_name' => 'تشخيص - مستوى 1 (تمييز الحروف)',
                'difficulty_level' => 1,
                'content_type' => 'assessment',
                'content_data' => json_encode([
                    'questions' => [
                        ['target' => 'ب', 'options' => ['ت', 'ب', 'ث', 'ن']],
                        ['target' => 'ج', 'options' => ['ح', 'خ', 'ج', 'ع']],
                        ['target' => 'س', 'options' => ['ش', 'ص', 'س', 'ض']],
                        ['target' => 'د', 'options' => ['ذ', 'د', 'ر', 'ز']],
                        ['target' => 'ط', 'options' => ['ظ', 'ط', 'ص', 'ض']],
                        ['target' => 'ع', 'options' => ['غ', 'ع', 'ح', 'خ']],
                        ['target' => 'ف', 'options' => ['ق', 'ف', 'غ', 'ع']],
                        ['target' => 'ص', 'options' => ['ض', 'ص', 'ط', 'ظ']],
                        ['target' => 'م', 'options' => ['م', 'ن', 'هـ', 'و']],
                        ['target' => 'ل', 'options' => ['ك', 'ل', 'م', 'ن']],
                        ['target' => 'هـ', 'options' => ['ع', 'غ', 'هـ', 'خ']],
                        ['target' => 'ك', 'options' => ['ل', 'ك', 'ع', 'غ']],
                        ['target' => 'ن', 'options' => ['ب', 'ت', 'ث', 'ن']],
                        ['target' => 'ي', 'options' => ['ئ', 'ي', 'ى', 'ب']],
                        ['target' => 'ر', 'options' => ['ز', 'ر', 'د', 'ذ']],
                    ]
                ], JSON_UNESCAPED_UNICODE),
            ],

            // 2. تشخيص عسر القراءة - المستوى 2 (15 سؤال - الانعكاس المرآتي)
            [
                'learning_difficulty_id' => 1,
                'level_name' => 'تشخيص - مستوى 2 (الانعكاس المرآتي)',
                'difficulty_level' => 2,
                'content_type' => 'assessment',
                'content_data' => json_encode([
                    'questions' => [
                        ['target' => '٢', 'options' => ['٦', '٢', '٣', '٤']], 
                        ['target' => '٧', 'options' => ['٨', '٧', '٦', '٢']],
                        ['target' => 'b', 'options' => ['d', 'b', 'p', 'q']], 
                        ['target' => 'p', 'options' => ['q', 'p', 'b', 'd']],
                        ['target' => 'بطة', 'options' => ['طبة', 'بطة', 'بظة', 'تطة']],
                        ['target' => 'قلم', 'options' => ['كلم', 'قلم', 'غلم', 'فلم']],
                        ['target' => 'كلب', 'options' => ['قلب', 'كلب', 'كاب', 'بلب']],
                        ['target' => 'باب', 'options' => ['ناب', 'تاب', 'باب', 'ثاب']],
                        ['target' => 'd', 'options' => ['b', 'p', 'q', 'd']],
                        ['target' => 'q', 'options' => ['p', 'b', 'd', 'q']],
                        ['target' => '٦', 'options' => ['٢', '٦', '٩', '٤']],
                        ['target' => '٩', 'options' => ['٦', '٩', '٥', '٤']],
                        ['target' => '٤', 'options' => ['٣', '٤', '٢', '٦']],
                        ['target' => 'W', 'options' => ['M', 'V', 'N', 'W']],
                        ['target' => 'm', 'options' => ['n', 'w', 'm', 'u']],
                    ]
                ], JSON_UNESCAPED_UNICODE),
            ],

            // 3. تشخيص عسر الحساب - المستوى 1 (15 سؤال - خدعة الأرقام المعكوسة)
            [
                'learning_difficulty_id' => 2,
                'level_name' => 'تشخيص - مستوى 1 (اتجاهات الأرقام)',
                'difficulty_level' => 1,
                'content_type' => 'assessment',
                'content_data' => json_encode([
                    'questions' => [
                        ['left' => 12, 'right' => 21, 'correct_side' => 'right'], 
                        ['left' => 45, 'right' => 54, 'correct_side' => 'right'],
                        ['left' => 13, 'right' => 31, 'correct_side' => 'right'],
                        ['left' => 67, 'right' => 76, 'correct_side' => 'right'],
                        ['left' => 98, 'right' => 89, 'correct_side' => 'left'],
                        ['left' => 32, 'right' => 23, 'correct_side' => 'left'],
                        ['left' => 15, 'right' => 51, 'correct_side' => 'right'],
                        ['left' => 82, 'right' => 28, 'correct_side' => 'left'],
                        ['left' => 14, 'right' => 41, 'correct_side' => 'right'],
                        ['left' => 73, 'right' => 37, 'correct_side' => 'left'],
                        ['left' => 26, 'right' => 62, 'correct_side' => 'right'],
                        ['left' => 91, 'right' => 19, 'correct_side' => 'left'],
                        ['left' => 17, 'right' => 71, 'correct_side' => 'right'],
                        ['left' => 53, 'right' => 35, 'correct_side' => 'left'],
                        ['left' => 84, 'right' => 48, 'correct_side' => 'left'],
                    ]
                ], JSON_UNESCAPED_UNICODE),
            ],

            // ==============================================================
            // 🟢 ثانياً: أسئلة التدريب (Training) - التدريب اليومي المستمر
            // ==============================================================

            // 4. تدريب عسر القراءة - مستوى 1 (15 سؤال)
            [
                'learning_difficulty_id' => 1,
                'level_name' => 'تدريب - المستوى السهل (تباين بصري)',
                'difficulty_level' => 1,
                'content_type' => 'training',
                'content_data' => json_encode([
                    'questions' => [
                        ['target' => 'أ', 'options' => ['أ', 'س', 'م', 'ط']],
                        ['target' => 'ك', 'options' => ['ع', 'ك', 'ص', 'و']],
                        ['target' => 'ل', 'options' => ['هـ', 'ق', 'ل', 'ي']],
                        ['target' => 'م', 'options' => ['د', 'ش', 'ف', 'م']],
                        ['target' => 'ن', 'options' => ['ح', 'ن', 'ط', 'ص']],
                        ['target' => 'و', 'options' => ['و', 'أ', 'ب', 'ت']],
                        ['target' => 'ي', 'options' => ['س', 'ي', 'م', 'ن']],
                        ['target' => 'ع', 'options' => ['ع', 'ب', 'ل', 'ك']],
                        ['target' => 'هـ', 'options' => ['م', 'ن', 'هـ', 'و']],
                        ['target' => 'ق', 'options' => ['ق', 'أ', 'د', 'ز']],
                        ['target' => 'س', 'options' => ['م', 'ل', 'س', 'و']],
                        ['target' => 'ط', 'options' => ['أ', 'ط', 'ف', 'ي']],
                        ['target' => 'ف', 'options' => ['د', 'ح', 'ف', 'ك']],
                        ['target' => 'ص', 'options' => ['ص', 'م', 'أ', 'ر']],
                        ['target' => 'د', 'options' => ['س', 'ع', 'م', 'د']],
                    ]
                ], JSON_UNESCAPED_UNICODE),
            ],

            // 5. تدريب عسر القراءة - مستوى 2 (15 سؤال)
            [
                'learning_difficulty_id' => 1,
                'level_name' => 'تدريب - المستوى المتوسط (القاعدة البنائية)',
                'difficulty_level' => 2,
                'content_type' => 'training',
                'content_data' => json_encode([
                    'questions' => [
                        ['target' => 'ح', 'options' => ['ح', 'خ', 'ع', 'غ']],
                        ['target' => 'ص', 'options' => ['ض', 'ص', 'ط', 'ظ']],
                        ['target' => 'س', 'options' => ['ش', 'ص', 'س', 'ض']],
                        ['target' => 'ع', 'options' => ['غ', 'ع', 'ح', 'خ']],
                        ['target' => 'ط', 'options' => ['ظ', 'ط', 'ص', 'ض']],
                        ['target' => 'ر', 'options' => ['ز', 'ر', 'و', 'د']],
                        ['target' => 'د', 'options' => ['ذ', 'د', 'ر', 'ز']],
                        ['target' => 'ت', 'options' => ['ث', 'ت', 'ب', 'ن']],
                        ['target' => 'ق', 'options' => ['ف', 'ق', 'غ', 'ع']],
                        ['target' => 'ظ', 'options' => ['ط', 'ظ', 'ض', 'ص']],
                        ['target' => 'خ', 'options' => ['ح', 'ج', 'خ', 'ع']],
                        ['target' => 'ض', 'options' => ['ص', 'ض', 'ظ', 'ط']],
                        ['target' => 'ش', 'options' => ['س', 'ش', 'ث', 'ق']],
                        ['target' => 'غ', 'options' => ['ع', 'غ', 'ف', 'ق']],
                        ['target' => 'ز', 'options' => ['ر', 'ز', 'ذ', 'د']],
                    ]
                ], JSON_UNESCAPED_UNICODE),
            ],

            // 6. تدريب عسر الحساب - مستوى 1 (15 سؤال)
            [
                'learning_difficulty_id' => 2,
                'level_name' => 'تدريب - فرق شاسع (سهل)',
                'difficulty_level' => 1,
                'content_type' => 'training',
                'content_data' => json_encode([
                    'questions' => [
                        ['left' => 10, 'right' => 2, 'correct_side' => 'left'],
                        ['left' => 1, 'right' => 8, 'correct_side' => 'right'],
                        ['left' => 9, 'right' => 3, 'correct_side' => 'left'],
                        ['left' => 2, 'right' => 11, 'correct_side' => 'right'],
                        ['left' => 12, 'right' => 4, 'correct_side' => 'left'],
                        ['left' => 3, 'right' => 15, 'correct_side' => 'right'],
                        ['left' => 14, 'right' => 5, 'correct_side' => 'left'],
                        ['left' => 4, 'right' => 12, 'correct_side' => 'right'],
                        ['left' => 16, 'right' => 6, 'correct_side' => 'left'],
                        ['left' => 5, 'right' => 20, 'correct_side' => 'right'],
                        ['left' => 15, 'right' => 3, 'correct_side' => 'left'],
                        ['left' => 2, 'right' => 14, 'correct_side' => 'right'],
                        ['left' => 18, 'right' => 4, 'correct_side' => 'left'],
                        ['left' => 5, 'right' => 15, 'correct_side' => 'right'],
                        ['left' => 12, 'right' => 2, 'correct_side' => 'left'],
                    ]
                ], JSON_UNESCAPED_UNICODE),
            ],

            // 7. تدريب عسر الحساب - مستوى 2 (15 سؤال)
            [
                'learning_difficulty_id' => 2,
                'level_name' => 'تدريب - فرق متوسط',
                'difficulty_level' => 2,
                'content_type' => 'training',
                'content_data' => json_encode([
                    'questions' => [
                        ['left' => 10, 'right' => 6, 'correct_side' => 'left'],
                        ['left' => 5, 'right' => 9, 'correct_side' => 'right'],
                        ['left' => 12, 'right' => 8, 'correct_side' => 'left'],
                        ['left' => 7, 'right' => 11, 'correct_side' => 'right'],
                        ['left' => 15, 'right' => 10, 'correct_side' => 'left'],
                        ['left' => 8, 'right' => 13, 'correct_side' => 'right'],
                        ['left' => 14, 'right' => 9, 'correct_side' => 'left'],
                        ['left' => 6, 'right' => 10, 'correct_side' => 'right'],
                        ['left' => 18, 'right' => 12, 'correct_side' => 'left'],
                        ['left' => 11, 'right' => 16, 'correct_side' => 'right'],
                        ['left' => 9, 'right' => 6, 'correct_side' => 'left'],
                        ['left' => 12, 'right' => 16, 'correct_side' => 'right'],
                        ['left' => 16, 'right' => 11, 'correct_side' => 'left'],
                        ['left' => 10, 'right' => 15, 'correct_side' => 'right'],
                        ['left' => 14, 'right' => 10, 'correct_side' => 'left'],
                    ]
                ], JSON_UNESCAPED_UNICODE),
            ]
        ];

        DB::table('game_contents')->insert($contents);
    }
}