<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameContentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('game_contents')->truncate(); // مسح القديم

        // ==============================================================
        // 🔴 أولاً: أسئلة التقييم (Assessment) - مبنية على معايير علمية
        // ==============================================================
        $assessmentData = [
            // --------------------------------------------------------------
            // 🧠 عسر القراءة (Dyslexia)
            // --------------------------------------------------------------
            [
                'learning_difficulty_id' => 1, 'level_name' => 'تقييم 1: التمييز البصري للحروف', 'difficulty_level' => 1, 'content_type' => 'assessment',
                'content_data' => json_encode(['questions' => [
                    ['target' => 'ب', 'options' => ['ت', 'ب', 'ث', 'ن']],
                    ['target' => 'ج', 'options' => ['ح', 'خ', 'ج', 'د']],
                    ['target' => 'س', 'options' => ['ش', 'س', 'ص', 'ض']],
                    ['target' => 'ث', 'options' => ['ب', 'ت', 'ث', 'ج']],
                    ['target' => 'ح', 'options' => ['ج', 'ح', 'خ', 'ع']],
                    ['target' => 'ش', 'options' => ['س', 'ش', 'ص', 'ح']],
                    ['target' => 'خ', 'options' => ['ج', 'ح', 'خ', 'ف']],
                    ['target' => 'ن', 'options' => ['ب', 'ن', 'م', 'ي']],
                    ['target' => 'د', 'options' => ['د', 'ذ', 'ر', 'ض']],
                    ['target' => 'ذ', 'options' => ['د', 'ذ', 'ز', 'ض']],
                    ['target' => 'ز', 'options' => ['ر', 'ز', 'ش', 'ع']],
                    ['target' => 'ر', 'options' => ['ر', 'ز', 'د', 'غ']],
                    ['target' => 'ع', 'options' => ['ع', 'غ', 'ق', 'ف']],
                    ['target' => 'غ', 'options' => ['ع', 'غ', 'خ', 'ق']],
                    ['target' => 'ق', 'options' => ['ق', 'ف', 'ك', 'ل']],
                ]], JSON_UNESCAPED_UNICODE),
            ],
            [
                'learning_difficulty_id' => 1, 'level_name' => 'تقييم 2: الانعكاس المرآتي', 'difficulty_level' => 2, 'content_type' => 'assessment',
                'content_data' => json_encode(['questions' => [
                    ['target' => 'ب', 'options' => ['د', 'ب', 'ج', 'ح']],
                    ['target' => 'د', 'options' => ['ب', 'د', 'ج', 'ع']],
                    ['target' => 'ج', 'options' => ['ج', 'خ', 'ح', 'ق']],
                    ['target' => 'خ', 'options' => ['ح', 'خ', 'ج', 'ض']],
                    ['target' => '2', 'options' => ['5', '6', '2', '8']],
                    ['target' => '6', 'options' => ['2', '6', '9', '8']],
                    ['target' => '7', 'options' => ['7', '8', '9', '4']],
                    ['target' => '8', 'options' => ['3', '7', '8', '9']],
                    ['target' => '9', 'options' => ['6', '9', '2', '8']],
                    ['target' => 'ن', 'options' => ['ن', 'ي', 'م', 'ب']],
                    ['target' => 'م', 'options' => ['م', 'و', 'ن', 'ه']],
                    ['target' => 'ع', 'options' => ['ع', 'غ', 'خ', 'ق']],
                    ['target' => 'ه', 'options' => ['ه', 'ة', 'ن', 'ب']],
                    ['target' => 'ق', 'options' => ['ق', 'ف', 'ل', 'و']],
                    ['target' => 'ف', 'options' => ['ف', 'ق', 'ك', 'ة']],
                ]], JSON_UNESCAPED_UNICODE),
            ],
            [
                'learning_difficulty_id' => 1, 'level_name' => 'تقييم 3: الوعي المقطعي والتشوه البصري', 'difficulty_level' => 3, 'content_type' => 'assessment',
                'content_data' => json_encode(['questions' => [
                    ['target' => 'بطة', 'options' => ['طبة', 'بظة', 'بطة', 'تبة']],
                    ['target' => 'كتاب', 'options' => ['تكاب', 'كباه', 'كتاب', 'بكاه']],
                    ['target' => 'ملح', 'options' => ['حلم', 'لحم', 'ملح', 'محل']],
                    ['target' => 'سمك', 'options' => ['كسم', 'سمك', 'مسك', 'مكس']],
                    ['target' => 'نجم', 'options' => ['جنم', 'منج', 'نجم', 'جمن']],
                    ['target' => 'قمر', 'options' => ['رقم', 'مقر', 'قمر', 'قرم']],
                    ['target' => 'حمار', 'options' => ['رحما', 'حرام', 'حمار', 'ماحر']],
                    ['target' => 'أرنب', 'options' => ['نرأب', 'برنا', 'أرنب', 'رنأب']],
                    ['target' => 'فراشة', 'options' => ['فاشرة', 'راشفة', 'فراشة', 'فراسة']],
                    ['target' => 'زهرة', 'options' => ['رهزة', 'زرهة', 'هزرة', 'زهرة']],
                    ['target' => 'طاولة', 'options' => ['تاولة', 'طولة', 'لطاو', 'طاولة']],
                    ['target' => 'شنطة', 'options' => ['نشطة', 'شطنة', 'طنشة', 'شنطة']],
                    ['target' => 'دجاجة', 'options' => ['جاجدة', 'جدجاة', 'دجاجة', 'داججة']],
                    ['target' => 'بلبل', 'options' => ['لبلب', 'بللب', 'لببل', 'بلبل']],
                    ['target' => 'خيار', 'options' => ['يخار', 'خيرا', 'خيار', 'رياخ']],
                ]], JSON_UNESCAPED_UNICODE),
            ],
            [
                'learning_difficulty_id' => 1, 'level_name' => 'تقييم 4: التمييز السمعي البصري', 'difficulty_level' => 4, 'content_type' => 'assessment',
                'content_data' => json_encode(['questions' => [
                    ['target' => 'كلب', 'options' => ['قلب', 'كلب', 'كهب', 'كلم']],
                    ['target' => 'قلب', 'options' => ['كلب', 'قلب', 'قهب', 'قلم']],
                    ['target' => 'تين', 'options' => ['طين', 'تين', 'تيم', 'سين']],
                    ['target' => 'طين', 'options' => ['تين', 'طين', 'طيم', 'تيح']],
                    ['target' => 'ذئب', 'options' => ['زئب', 'ذئب', 'ديب', 'ذيب']],
                    ['target' => 'زئب', 'options' => ['ذئب', 'زئب', 'ويب', 'زيب']],
                    ['target' => 'سم', 'options' => ['ثم', 'سم', 'سن', 'شم']],
                    ['target' => 'ثم', 'options' => ['سم', 'ثم', 'ثن', 'شم']],
                    ['target' => 'ضاد', 'options' => ['صاد', 'ضاد', 'طاد', 'باد']],
                    ['target' => 'صاد', 'options' => ['ضاد', 'صاد', 'سادّ', 'شاد']],
                    ['target' => 'ساعة', 'options' => ['شاعة', 'ساعة', 'ساعي', 'سايع']],
                    ['target' => 'شاعر', 'options' => ['ساعر', 'شاعر', 'سالر', 'شاله']],
                    ['target' => 'قصة', 'options' => ['كصة', 'قصة', 'قسة', 'كسة']],
                    ['target' => 'كاس', 'options' => ['قاس', 'كاس', 'كاه', 'قاه']],
                    ['target' => 'ذهب', 'options' => ['زهب', 'ذهب', 'ديب', 'ذيب']],
                ]], JSON_UNESCAPED_UNICODE),
            ],

            // --------------------------------------------------------------
            // 🧮 عسر الحساب (Dyscalculia)
            // --------------------------------------------------------------
            [
                'learning_difficulty_id' => 2, 'level_name' => 'تقييم 1: الخلط المكاني وخانات الآحاد والعشرات', 'difficulty_level' => 1, 'content_type' => 'assessment',
                'content_data' => json_encode(['questions' => [
                    ['left' => 21, 'right' => 12, 'correct_side' => 'left'],
                    ['left' => 13, 'right' => 31, 'correct_side' => 'right'], // تم عكسها للمنطق
                    ['left' => 41, 'right' => 14, 'correct_side' => 'left'],
                    ['left' => 15, 'right' => 51, 'correct_side' => 'right'], // تم عكسها
                    ['left' => 32, 'right' => 23, 'correct_side' => 'left'],
                    ['left' => 24, 'right' => 42, 'correct_side' => 'right'], // تم عكسها
                    ['left' => 52, 'right' => 25, 'correct_side' => 'left'],
                    ['left' => 34, 'right' => 43, 'correct_side' => 'right'], // تم عكسها
                    ['left' => 53, 'right' => 35, 'correct_side' => 'left'],
                    ['left' => 45, 'right' => 54, 'correct_side' => 'right'], // تم عكسها
                    ['left' => 61, 'right' => 16, 'correct_side' => 'left'],
                    ['left' => 17, 'right' => 71, 'correct_side' => 'right'], // تم عكسها
                    ['left' => 81, 'right' => 18, 'correct_side' => 'left'],
                    ['left' => 19, 'right' => 91, 'correct_side' => 'right'], // تم عكسها
                    ['left' => 62, 'right' => 26, 'correct_side' => 'left'],
                ]], JSON_UNESCAPED_UNICODE),
            ],
            [
                'learning_difficulty_id' => 2, 'level_name' => 'تقييم 2: إدراك الكميات - فروق شاسعة', 'difficulty_level' => 2, 'content_type' => 'assessment',
                'content_data' => json_encode(['questions' => [
                    ['left' => 18, 'right' => 2, 'correct_side' => 'left'],
                    ['left' => 3, 'right' => 20, 'correct_side' => 'right'], // تم عكسها
                    ['left' => 25, 'right' => 5, 'correct_side' => 'left'],
                    ['left' => 7, 'right' => 30, 'correct_side' => 'right'], // تم عكسها
                    ['left' => 28, 'right' => 4, 'correct_side' => 'left'],
                    ['left' => 6, 'right' => 35, 'correct_side' => 'right'], // تم عكسها
                    ['left' => 40, 'right' => 8, 'correct_side' => 'left'],
                    ['left' => 9, 'right' => 45, 'correct_side' => 'right'], // تم عكسها
                    ['left' => 50, 'right' => 10, 'correct_side' => 'left'],
                    ['left' => 12, 'right' => 60, 'correct_side' => 'right'], // تم عكسها
                    ['left' => 70, 'right' => 15, 'correct_side' => 'left'],
                    ['left' => 1, 'right' => 19, 'correct_side' => 'right'], // تم عكسها
                    ['left' => 55, 'right' => 11, 'correct_side' => 'left'],
                    ['left' => 13, 'right' => 65, 'correct_side' => 'right'], // تم عكسها
                    ['left' => 75, 'right' => 14, 'correct_side' => 'left'],
                ]], JSON_UNESCAPED_UNICODE),
            ],
            [
                'learning_difficulty_id' => 2, 'level_name' => 'تقييم 3: إدراك الكميات - فروق متقاربة', 'difficulty_level' => 3, 'content_type' => 'assessment',
                'content_data' => json_encode(['questions' => [
                    ['left' => 9, 'right' => 8, 'correct_side' => 'left'],
                    ['left' => 7, 'right' => 8, 'correct_side' => 'right'], // تم عكسها
                    ['left' => 16, 'right' => 15, 'correct_side' => 'left'],
                    ['left' => 14, 'right' => 15, 'correct_side' => 'right'], // تم عكسها
                    ['left' => 24, 'right' => 23, 'correct_side' => 'left'],
                    ['left' => 22, 'right' => 23, 'correct_side' => 'right'], // تم عكسها
                    ['left' => 20, 'right' => 19, 'correct_side' => 'left'],
                    ['left' => 29, 'right' => 30, 'correct_side' => 'right'], // تم عكسها
                    ['left' => 39, 'right' => 38, 'correct_side' => 'left'],
                    ['left' => 45, 'right' => 46, 'correct_side' => 'right'], // تم عكسها
                    ['left' => 60, 'right' => 59, 'correct_side' => 'left'],
                    ['left' => 67, 'right' => 68, 'correct_side' => 'right'], // تم عكسها
                    ['left' => 74, 'right' => 73, 'correct_side' => 'left'],
                    ['left' => 81, 'right' => 82, 'correct_side' => 'right'], // تم عكسها
                    ['left' => 100, 'right' => 99, 'correct_side' => 'left'],
                ]], JSON_UNESCAPED_UNICODE),
            ],
            [
                'learning_difficulty_id' => 2, 'level_name' => 'تقييم 4: المتتاليات والأنماط العددية', 'difficulty_level' => 4, 'content_type' => 'assessment',
                'content_data' => json_encode(['questions' => [
                    ['target' => '2, 4, _, 8', 'options' => ['5', '6', '7', '9']],
                    ['target' => '1, 2, 3, _', 'options' => ['4', '5', '6', '7']],
                    ['target' => '5, 10, _, 20', 'options' => ['12', '13', '15', '18']],
                    ['target' => '10, 20, 30, _', 'options' => ['35', '40', '45', '50']],
                    ['target' => '3, 6, 9, _', 'options' => ['10', '11', '12', '15']],
                    ['target' => '2, 4, 6, _', 'options' => ['7', '8', '9', '12']],
                    ['target' => '1, 3, 5, _', 'options' => ['6', '7', '8', '9']],
                    ['target' => '10, 9, 8, _', 'options' => ['5', '6', '7', '9']],
                    ['target' => '20, 18, 16, _', 'options' => ['12', '13', '14', '15']],
                    ['target' => '5, 10, 15, _', 'options' => ['20', '21', '22', '25']],
                    ['target' => '7, 14, 21, _', 'options' => ['25', '26', '27', '28']],
                    ['target' => '4, 8, 12, _', 'options' => ['14', '15', '16', '20']],
                    ['target' => '100, 90, 80, _', 'options' => ['60', '65', '70', '75']],
                    ['target' => '1, 4, 7, _', 'options' => ['8', '9', '10', '15']],
                    ['target' => '50, 40, 30, _', 'options' => ['20', '21', '22', '25']],
                ]], JSON_UNESCAPED_UNICODE),
            ],
        ];
        // ==============================================================
        // 🟢 ثانياً: أسئلة التدريب (Training) - بنك أسئلة ضخم وثابت
        // ==============================================================
        $trainingData = [
            // --------------------------------------------------------------
            // 🧠 عسر القراءة (Dyslexia) - التدريب
            // --------------------------------------------------------------
            [
                'learning_difficulty_id' => 1, 'level_name' => 'تدريب 1: التمييز البصري للحروف', 'difficulty_level' => 1, 'content_type' => 'training',
                'content_data' => json_encode(['questions' => [
                    ['target' => 'ت', 'options' => ['ب', 'ت', 'ث', 'ج']], ['target' => 'ح', 'options' => ['ج', 'ح', 'خ', 'د']],
                    ['target' => 'ص', 'options' => ['س', 'ش', 'ص', 'ض']], ['target' => 'ض', 'options' => ['ص', 'ض', 'ط', 'ع']],
                    ['target' => 'ط', 'options' => ['ض', 'ط', 'ظ', 'ق']], ['target' => 'ظ', 'options' => ['ط', 'ظ', 'ع', 'غ']],
                    ['target' => 'ف', 'options' => ['ق', 'ف', 'ك', 'ل']], ['target' => 'ك', 'options' => ['ف', 'ك', 'ل', 'م']],
                    ['target' => 'ل', 'options' => ['ك', 'ل', 'م', 'ن']], ['target' => 'م', 'options' => ['ل', 'م', 'ن', 'ه']],
                    ['target' => 'ه', 'options' => ['م', 'ه', 'و', 'ي']], ['target' => 'و', 'options' => ['ه', 'و', 'ي', 'ا']],
                    ['target' => 'ي', 'options' => ['و', 'ي', 'ا', 'أ']], ['target' => 'ا', 'options' => ['ي', 'ا', 'أ', 'ع']],
                    ['target' => 'أ', 'options' => ['ا', 'أ', 'إ', 'ؤ']], ['target' => 'إ', 'options' => ['أ', 'إ', 'ئ', 'ؤ']],
                    ['target' => 'ة', 'options' => ['ه', 'ة', 'ع', 'ق']], ['target' => 'ب', 'options' => ['ج', 'ب', 'د', 'ح']],
                    ['target' => 'ج', 'options' => ['ب', 'ج', 'د', 'خ']], ['target' => 'د', 'options' => ['ج', 'د', 'ذ', 'ر']],
                    ['target' => 'ذ', 'options' => ['د', 'ذ', 'ز', 'ض']], ['target' => 'ر', 'options' => ['ذ', 'ر', 'ز', 'و']],
                    ['target' => 'ز', 'options' => ['ر', 'ز', 'ش', 'س']], ['target' => 'س', 'options' => ['ز', 'س', 'ش', 'ص']],
                    ['target' => 'ش', 'options' => ['س', 'ش', 'ص', 'ط']], ['target' => 'ع', 'options' => ['ش', 'ع', 'غ', 'ف']],
                    ['target' => 'غ', 'options' => ['ع', 'غ', 'ق', 'خ']], ['target' => 'ق', 'options' => ['غ', 'ق', 'ف', 'ك']],
                    ['target' => 'خ', 'options' => ['ح', 'خ', 'ق', 'ع']], ['target' => 'ن', 'options' => ['م', 'ن', 'ي', 'ب']],
                ]], JSON_UNESCAPED_UNICODE),
            ],
            [
                'learning_difficulty_id' => 1, 'level_name' => 'تدريب 2: الانعكاس المرآتي', 'difficulty_level' => 2, 'content_type' => 'training',
                'content_data' => json_encode(['questions' => [
                    ['target' => 'ب', 'options' => ['د', 'ب', 'ج', 'ح']], ['target' => 'ج', 'options' => ['خ', 'ج', 'ح', 'ق']],
                    ['target' => 'خ', 'options' => ['ح', 'خ', 'ج', 'ض']], ['target' => 'د', 'options' => ['ب', 'د', 'ج', 'ع']],
                    ['target' => 'ع', 'options' => ['ع', 'غ', 'خ', 'ق']], ['target' => '2', 'options' => ['5', '6', '2', '8']],
                    ['target' => '6', 'options' => ['2', '6', '9', '8']], ['target' => '9', 'options' => ['6', '9', '2', '8']],
                    ['target' => '7', 'options' => ['7', '8', '9', '4']], ['target' => '8', 'options' => ['3', '7', '8', '9']],
                    ['target' => 'ن', 'options' => ['ن', 'ي', 'م', 'ب']], ['target' => 'م', 'options' => ['م', 'و', 'ن', 'ه']],
                    ['target' => 'ي', 'options' => ['ن', 'ي', 'ب', 'ر']], ['target' => 'ه', 'options' => ['ه', 'ة', 'ن', 'ب']],
                    ['target' => 'ق', 'options' => ['ق', 'ف', 'ل', 'و']], ['target' => 'ف', 'options' => ['ف', 'ق', 'ك', 'ة']],
                    ['target' => 'ك', 'options' => ['ك', 'ق', 'ف', 'ل']], ['target' => 'ع', 'options' => ['غ', 'ع', 'خ', 'ق']],
                    ['target' => 'غ', 'options' => ['ع', 'غ', 'خ', 'ق']], ['target' => 'ح', 'options' => ['ج', 'ح', 'خ', 'د']],
                    ['target' => 'و', 'options' => ['و', 'ه', 'م', 'ب']], ['target' => 'ل', 'options' => ['ل', 'ك', 'ق', 'ف']],
                    ['target' => '3', 'options' => ['3', '5', '8', '2']], ['target' => '5', 'options' => ['2', '5', '6', '8']],
                    ['target' => '4', 'options' => ['4', '6', '7', '9']], ['target' => 'ر', 'options' => ['ز', 'ر', 'د', 'ذ']],
                    ['target' => 'ز', 'options' => ['ر', 'ز', 'ش', 'ع']], ['target' => 'س', 'options' => ['ش', 'س', 'ص', 'ض']],
                    ['target' => 'ش', 'options' => ['س', 'ش', 'ص', 'ح']], ['target' => 'ص', 'options' => ['س', 'ص', 'ض', 'ط']],
                ]], JSON_UNESCAPED_UNICODE),
            ],
            [
                'learning_difficulty_id' => 1, 'level_name' => 'تدريب 3: الوعي المقطعي والتشوه البصري', 'difficulty_level' => 3, 'content_type' => 'training',
                'content_data' => json_encode(['questions' => [
                    ['target' => 'تفاحة', 'options' => ['فتاحة', 'تاحف', 'تفاحة', 'حفاتة']], ['target' => 'موز', 'options' => ['زوم', 'موز', 'زوم', 'وزم']],
                    ['target' => 'برتقال', 'options' => ['تبرقال', 'برتقال', 'برقتال', 'تقبرال']], ['target' => 'عنب', 'options' => ['نعب', 'بعن', 'عنب', 'نبع']],
                    ['target' => 'رمان', 'options' => ['نرام', 'مران', 'رمان', 'نمار']], ['target' => 'جزر', 'options' => ['رجز', 'جرز', 'جزر', 'زرج']],
                    ['target' => 'خيار', 'options' => ['يخار', 'خيار', 'رياخ', 'يراخ']], ['target' => 'بصل', 'options' => ['صبل', 'لصب', 'بصل', 'سبل']],
                    ['target' => 'ثوم', 'options' => ['وثم', 'موث', 'ثوم', 'متو']], ['target' => 'فلفل', 'options' => ['لفلف', 'فللف', 'فلفل', 'لفف']],
                    ['target' => 'ملح', 'options' => ['حلم', 'لحم', 'ملح', 'محل']], ['target' => 'سكر', 'options' => ['رسك', 'كسر', 'سكر', 'رك']],
                    ['target' => 'زيت', 'options' => ['يزت', 'تيز', 'زيت', 'يتز']], ['target' => 'عسل', 'options' => ['سعل', 'لسع', 'عسل', 'سلع']],
                    ['target' => 'حليب', 'options' => ['لحيب', 'حلبي', 'حليب', 'بيحل']], ['target' => 'جبن', 'options' => ['نجب', 'بجن', 'جبن', 'نبج']],
                    ['target' => 'ماء', 'options' => ['وما', 'ام', 'ماء', 'امو']], ['target' => 'شاي', 'options' => ['يشا', 'اش', 'شاي', 'ايش']],
                    ['target' => 'قهوة', 'options' => ['وهقة', 'قهوة', 'هوقة', 'وقهة']], ['target' => 'لحم', 'options' => ['حمل', 'ملح', 'لحم', 'حلم']],
                    ['target' => 'دجاج', 'options' => ['جاجد', 'جدجا', 'دجاج', 'جادج']], ['target' => 'سمك', 'options' => ['كسم', 'مسك', 'سمك', 'كمس']],
                    ['target' => 'دقيق', 'options' => ['قديق', 'دقيق', 'ديقق', 'قيدق']], ['target' => 'بيض', 'options' => ['ضيب', 'يضب', 'بيض', 'ضبي']],
                    ['target' => 'جبانة', 'options' => ['بجانة', 'جانبة', 'جبانة', 'نباج']], ['target' => 'سلطة', 'options' => ['لطسة', 'سلطة', 'طسلة', 'لسطة']],
                    ['target' => 'حساء', 'options' => ['ساحء', 'حسءا', 'حساء', 'اءحس']], ['target' => 'معكرونة', 'options' => ['معرونك', 'معكرونة', 'رونمعك', 'كرونمع']],
                    ['target' => 'فشار', 'options' => ['شاف', 'راشف', 'فشار', 'رافش']], ['target' => 'كيك', 'options' => ['كيك', 'ككي', 'يكك', 'كك']],
                ]], JSON_UNESCAPED_UNICODE),
            ],
            [
                'learning_difficulty_id' => 1, 'level_name' => 'تدريب 4: التمييز السمعي البصري', 'difficulty_level' => 4, 'content_type' => 'training',
                'content_data' => json_encode(['questions' => [
                    ['target' => 'قلم', 'options' => ['كلم', 'قلم', 'قهم', 'كلب']], ['target' => 'سلم', 'options' => ['ثلم', 'سلم', 'شلم', 'سهم']],
                    ['target' => 'رأس', 'options' => ['زأس', 'رأس', 'رأم', 'ذأس']], ['target' => 'زأس', 'options' => ['رأس', 'زأس', 'سأز', 'ساز']],
                    ['target' => 'نور', 'options' => ['نول', 'نور', 'مول', 'نوم']], ['target' => 'مور', 'options' => ['نور', 'مور', 'مول', 'دول']],
                    ['target' => 'صادق', 'options' => ['ضادق', 'صادق', 'سادق', 'شادق']], ['target' => 'ضادق', 'options' => ['صادق', 'ضادق', 'طادق', 'باق']],
                    ['target' => 'سار', 'options' => ['ثار', 'سار', 'شار', 'صار']], ['target' => 'ثار', 'options' => ['سار', 'ثار', 'شار', 'سأر']],
                    ['target' => 'كبة', 'options' => ['قبة', 'كبة', 'كبيب', 'قيبة']], ['target' => 'قبة', 'options' => ['كبة', 'قبة', 'كيبة', 'كبه']],
                    ['target' => 'تاج', 'options' => ['طاج', 'تاج', 'تاح', 'طاح']], ['target' => 'طاج', 'options' => ['تاج', 'طاج', 'تاج', 'طاب']],
                    ['target' => 'دار', 'options' => ['ذار', 'دار', 'درب', 'ذاب']], ['target' => 'ذار', 'options' => ['دار', 'ذار', 'داب', 'ذاب']],
                    ['target' => 'خانة', 'options' => ['جانة', 'خانة', 'خاشة', 'حانة']], ['target' => 'جانة', 'options' => ['خانة', 'جانة', 'جاشة', 'حجانة']],
                    ['target' => 'سام', 'options' => ['ثام', 'سام', 'شام', 'ضام']], ['target' => 'شام', 'options' => ['سام', 'شام', 'ثام', 'صام']],
                    ['target' => 'عسكر', 'options' => ['غسكر', 'عسكر', 'سعكر', 'عشكر']], ['target' => 'غسكر', 'options' => ['عسكر', 'غسكر', 'عغسكر', 'غشكر']],
                    ['target' => 'سلام', 'options' => ['ثلام', 'سلام', 'شلام', 'صلام']], ['target' => 'ثلام', 'options' => ['سلام', 'ثلام', 'شلام', 'صلام']],
                    ['target' => 'كسب', 'options' => ['قسب', 'كسب', 'كشب', 'قشب']], ['target' => 'قسب', 'options' => ['كسب', 'قسب', 'قشب', 'كشب']],
                    ['target' => 'داخل', 'options' => ['ذاخل', 'داخل', 'داح', 'ذاح']], ['target' => 'ذاخل', 'options' => ['داخل', 'ذاخل', 'داح', 'ذاح']],
                    ['target' => 'ضيعة', 'options' => ['صيعة', 'ضيعة', 'طيعة', 'بيعة']], ['target' => 'صيعة', 'options' => ['ضيعة', 'صيعة', 'طيعة', 'سيعة']],
                ]], JSON_UNESCAPED_UNICODE),
            ],

            // --------------------------------------------------------------
            // 🧮 عسر الحساب (Dyscalculia) - التدريب
            // --------------------------------------------------------------
            [
                'learning_difficulty_id' => 2, 'level_name' => 'تدريب 1: الخلط المكاني', 'difficulty_level' => 1, 'content_type' => 'training',
                'content_data' => json_encode(['questions' => [
                    ['left' => 61, 'right' => 16, 'correct_side' => 'left'], ['left' => 71, 'right' => 17, 'correct_side' => 'left'],
                    ['left' => 81, 'right' => 18, 'correct_side' => 'left'], ['left' => 91, 'right' => 19, 'correct_side' => 'left'],
                    ['left' => 62, 'right' => 26, 'correct_side' => 'left'], ['left' => 72, 'right' => 27, 'correct_side' => 'left'],
                    ['left' => 82, 'right' => 28, 'correct_side' => 'left'], ['left' => 92, 'right' => 29, 'correct_side' => 'left'],
                    ['left' => 63, 'right' => 36, 'correct_side' => 'left'], ['left' => 73, 'right' => 37, 'correct_side' => 'left'],
                    ['left' => 83, 'right' => 38, 'correct_side' => 'left'], ['left' => 93, 'right' => 39, 'correct_side' => 'left'],
                    ['left' => 64, 'right' => 46, 'correct_side' => 'left'], ['left' => 74, 'right' => 47, 'correct_side' => 'left'],
                    ['left' => 84, 'right' => 48, 'correct_side' => 'left'], ['left' => 94, 'right' => 49, 'correct_side' => 'left'],
                    ['left' => 65, 'right' => 56, 'correct_side' => 'left'], ['left' => 75, 'right' => 57, 'correct_side' => 'left'],
                    ['left' => 85, 'right' => 58, 'correct_side' => 'left'], ['left' => 95, 'right' => 59, 'correct_side' => 'left'],
                    ['left' => 2, 'right' => 20, 'correct_side' => 'right'], ['left' => 3, 'right' => 30, 'correct_side' => 'right'],
                    ['left' => 4, 'right' => 40, 'correct_side' => 'right'], ['left' => 5, 'right' => 50, 'correct_side' => 'right'],
                    ['left' => 6, 'right' => 60, 'correct_side' => 'right'], ['left' => 7, 'right' => 70, 'correct_side' => 'right'],
                    ['left' => 8, 'right' => 80, 'correct_side' => 'right'], ['left' => 9, 'right' => 90, 'correct_side' => 'right'],
                    ['left' => 12, 'right' => 11, 'correct_side' => 'left'], ['left' => 1, 'right' => 10, 'correct_side' => 'right'],
                ]], JSON_UNESCAPED_UNICODE),
            ],
            [
                'learning_difficulty_id' => 2, 'level_name' => 'تدريب 2: إدراك الكميات - فروق شاسعة', 'difficulty_level' => 2, 'content_type' => 'training',
                'content_data' => json_encode(['questions' => [
                    ['left' => 15, 'right' => 1, 'correct_side' => 'left'], ['left' => 22, 'right' => 2, 'correct_side' => 'left'],
                    ['left' => 30, 'right' => 3, 'correct_side' => 'left'], ['left' => 32, 'right' => 4, 'correct_side' => 'left'],
                    ['left' => 40, 'right' => 5, 'correct_side' => 'left'], ['left' => 48, 'right' => 6, 'correct_side' => 'left'],
                    ['left' => 50, 'right' => 7, 'correct_side' => 'left'], ['left' => 58, 'right' => 8, 'correct_side' => 'left'],
                    ['left' => 60, 'right' => 9, 'correct_side' => 'left'], ['left' => 70, 'right' => 11, 'correct_side' => 'left'],
                    ['left' => 80, 'right' => 12, 'correct_side' => 'left'], ['left' => 85, 'right' => 13, 'correct_side' => 'left'],
                    ['left' => 90, 'right' => 14, 'correct_side' => 'left'], ['left' => 95, 'right' => 16, 'correct_side' => 'left'],
                    ['left' => 99, 'right' => 17, 'correct_side' => 'left'], ['left' => 5, 'right' => 50, 'correct_side' => 'right'],
                    ['left' => 8, 'right' => 40, 'correct_side' => 'right'], ['left' => 6, 'right' => 60, 'correct_side' => 'right'],
                    ['left' => 12, 'right' => 70, 'correct_side' => 'right'], ['left' => 15, 'right' => 80, 'correct_side' => 'right'],
                    ['left' => 20, 'right' => 90, 'correct_side' => 'right'], ['left' => 25, 'right' => 100, 'correct_side' => 'right'],
                    ['left' => 25, 'right' => 3, 'correct_side' => 'left'], ['left' => 45, 'right' => 7, 'correct_side' => 'left'],
                    ['left' => 65, 'right' => 10, 'correct_side' => 'left'], ['left' => 75, 'right' => 18, 'correct_side' => 'left'],
                    ['left' => 88, 'right' => 22, 'correct_side' => 'left'], ['left' => 10, 'right' => 30, 'correct_side' => 'right'],
                    ['left' => 18, 'right' => 55, 'correct_side' => 'right'], ['left' => 11, 'right' => 42, 'correct_side' => 'right'],
                ]], JSON_UNESCAPED_UNICODE),
            ],
            [
                'learning_difficulty_id' => 2, 'level_name' => 'تدريب 3: إدراك الكميات - فروق متقاربة', 'difficulty_level' => 3, 'content_type' => 'training',
                'content_data' => json_encode(['questions' => [
                    ['left' => 11, 'right' => 10, 'correct_side' => 'left'], ['left' => 10, 'right' => 9, 'correct_side' => 'left'],
                    ['left' => 7, 'right' => 6, 'correct_side' => 'left'], ['left' => 13, 'right' => 12, 'correct_side' => 'left'],
                    ['left' => 21, 'right' => 20, 'correct_side' => 'left'], ['left' => 18, 'right' => 17, 'correct_side' => 'left'],
                    ['left' => 25, 'right' => 24, 'correct_side' => 'left'], ['left' => 32, 'right' => 31, 'correct_side' => 'left'],
                    ['left' => 26, 'right' => 25, 'correct_side' => 'left'], ['left' => 40, 'right' => 39, 'correct_side' => 'left'],
                    ['left' => 45, 'right' => 44, 'correct_side' => 'left'], ['left' => 49, 'right' => 48, 'correct_side' => 'left'],
                    ['left' => 52, 'right' => 51, 'correct_side' => 'left'], ['left' => 59, 'right' => 58, 'correct_side' => 'left'],
                    ['left' => 64, 'right' => 63, 'correct_side' => 'left'], ['left' => 71, 'right' => 70, 'correct_side' => 'left'],
                    ['left' => 77, 'right' => 76, 'correct_side' => 'left'], ['left' => 83, 'right' => 82, 'correct_side' => 'left'],
                    ['left' => 90, 'right' => 89, 'correct_side' => 'left'], ['left' => 96, 'right' => 95, 'correct_side' => 'left'],
                    ['left' => 10, 'right' => 11, 'correct_side' => 'right'], ['left' => 11, 'right' => 12, 'correct_side' => 'right'],
                    ['left' => 21, 'right' => 22, 'correct_side' => 'right'], ['left' => 32, 'right' => 33, 'correct_side' => 'right'],
                    ['left' => 44, 'right' => 45, 'correct_side' => 'right'], ['left' => 55, 'right' => 56, 'correct_side' => 'right'],
                    ['left' => 66, 'right' => 67, 'correct_side' => 'right'], ['left' => 77, 'right' => 78, 'correct_side' => 'right'],
                    ['left' => 83, 'right' => 84, 'correct_side' => 'right'], ['left' => 99, 'right' => 100, 'correct_side' => 'right'],
                ]], JSON_UNESCAPED_UNICODE),
            ],
            [
                'learning_difficulty_id' => 2, 'level_name' => 'تدريب 4: المتتاليات والأنماط العددية', 'difficulty_level' => 4, 'content_type' => 'training',
                'content_data' => json_encode(['questions' => [
                    ['target' => '1, 2, _, 4', 'options' => ['2', '3', '5', '6']], ['target' => '3, 6, _, 12', 'options' => ['8', '9', '10', '11']],
                    ['target' => '5, 10, _, 20', 'options' => ['12', '13', '14', '15']], ['target' => '2, 4, 6, _', 'options' => ['7', '8', '9', '10']],
                    ['target' => '10, 20, _, 40', 'options' => ['25', '30', '35', '50']], ['target' => '1, 3, 5, _', 'options' => ['6', '7', '8', '9']],
                    ['target' => '4, 8, 12, _', 'options' => ['15', '16', '17', '20']], ['target' => '5, 15, _, 45', 'options' => ['25', '30', '35', '40']],
                    ['target' => '2, 6, 10, _', 'options' => ['12', '13', '14', '15']], ['target' => '7, 14, 21, _', 'options' => ['25', '26', '27', '28']],
                    ['target' => '10, 9, 8, _', 'options' => ['5', '6', '7', '9']], ['target' => '20, 18, 16, _', 'options' => ['12', '13', '14', '15']],
                    ['target' => '50, 40, 30, _', 'options' => ['20', '21', '22', '25']], ['target' => '100, 90, 80, _', 'options' => ['60', '65', '70', '75']],
                    ['target' => '9, 8, 7, _', 'options' => ['4', '5', '6', '8']], ['target' => '1, 4, 7, _', 'options' => ['8', '9', '10', '15']],
                    ['target' => '2, 5, 8, _', 'options' => ['9', '10', '11', '12']], ['target' => '3, 7, 11, _', 'options' => ['13', '14', '15', '16']],
                    ['target' => '6, 12, _, 24', 'options' => ['16', '17', '18', '19']], ['target' => '8, 16, _, 32', 'options' => ['20', '22', '23', '24']],
                    ['target' => '25, 20, 15, _', 'options' => ['8', '9', '10', '12']], ['target' => '30, 25, 20, _', 'options' => ['12', '13', '14', '15']],
                    ['target' => '3, 9, _, 27', 'options' => ['15', '16', '17', '18']], ['target' => '4, 12, _, 36', 'options' => ['20', '22', '24', '28']],
                    ['target' => '2, 8, _, 32', 'options' => ['16', '18', '20', '22']], ['target' => '5, 25, _, 625', 'options' => ['100', '125', '150', '175']],
                    ['target' => '1, 5, 9, _', 'options' => ['12', '13', '14', '15']], ['target' => '11, 22, 33, _', 'options' => ['40', '42', '44', '50']],
                    ['target' => '15, 30, _, 60', 'options' => ['40', '42', '44', '45']], ['target' => '6, 11, 16, _', 'options' => ['20', '21', '22', '25']],
                ]], JSON_UNESCAPED_UNICODE),
            ],
        ];

        // نقوم بدمج التقييم مع التدريب ثم إدخالهم جميعاً إلى قاعدة البيانات
        $allContents = array_merge($assessmentData, $trainingData);
        DB::table('game_contents')->insert($allContents);

        
    }
}