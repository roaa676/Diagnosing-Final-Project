<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameContentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('game_contents')->truncate();

        $records = [
            $this->contentRecord(1, 'التقييم التشخيصي', 1, 'assessment', $this->assessmentDiagnosticQuestions()),

            $this->contentRecord(1, 'التدريب - المستوى السهل (القراءة)', 1, 'training', $this->dyslexiaTrainingLevelOne()),
            $this->contentRecord(1, 'التدريب - المستوى المتوسط (القراءة)', 2, 'training', $this->dyslexiaTrainingLevelTwo()),
            $this->contentRecord(1, 'التدريب - المستوى المتقدم (القراءة)', 3, 'training', $this->dyslexiaTrainingLevelThree()),

            $this->contentRecord(2, 'التدريب - المستوى السهل (الحساب)', 1, 'training', $this->dyscalculiaTrainingLevelOne()),
            $this->contentRecord(2, 'التدريب - المستوى المتوسط (الحساب)', 2, 'training', $this->dyscalculiaTrainingLevelTwo()),
            $this->contentRecord(2, 'التدريب - المستوى المتقدم (الحساب)', 3, 'training', $this->dyscalculiaTrainingLevelThree()),
        ];

        DB::table('game_contents')->insert($records);
    }

    private function contentRecord(int $learningDifficultyId, string $levelName, int $difficultyLevel, string $contentType, array $questions): array
    {
        return [
            'learning_difficulty_id' => $learningDifficultyId,
            'level_name' => $levelName,
            'difficulty_level' => $difficultyLevel,
            'content_type' => $contentType,
            'content_data' => json_encode([
                'questions' => $questions,
            ], JSON_UNESCAPED_UNICODE),
        ];
    }

    private function question(string $question, array $options, string $correctAnswer, ?string $category = null): array
    {
        $payload = [
            'question' => $question,
            'options' => $options,
            'correct_answer' => $correctAnswer,
        ];

        if ($category !== null) {
            $payload['category'] = $category;
        }

        return $payload;
    }

    private function assessmentDiagnosticQuestions(): array
    {
        return [
            $this->question('ما الحرف الأول في كلمة "باب"؟', ['ب', 'ت', 'ث', 'ج'], 'ب', 'reading'),
            $this->question('أكمل الكلمة: ك_تاب', ['ت', 'ب', 'ا', 'م'], 'ت', 'reading'),
            $this->question('اختر الكلمة الصحيحة: "كتاب"', ['كتاب', 'كتايب', 'كتتب', 'كثاب'], 'كتاب', 'reading'),
            $this->question('ما الحرف الأخير في كلمة "قلم"؟', ['ق', 'ل', 'م', 'ن'], 'م', 'reading'),
            $this->question('أي كلمة تبدأ بحرف "ف"؟', ['فراشة', 'شمس', 'بيت', 'قمر'], 'فراشة', 'reading'),
            $this->question('أي كلمة تدل على مكان التعلم؟', ['مدرسة', 'سيارة', 'شجرة', 'طائرة'], 'مدرسة', 'reading'),
            $this->question('ما الحرف الناقص في كلمة "_مر"؟', ['ق', 'ك', 'ع', 'ب'], 'ق', 'reading'),
            $this->question('أي كلمة نقرأ بها القصص؟', ['كتاب', 'قلم', 'كرسي', 'باب'], 'كتاب', 'reading'),
            $this->question('أي كلمة فيها حرف "ش"؟', ['شمس', 'قمر', 'بيت', 'وردة'], 'شمس', 'reading'),

            $this->question('ما العدد التالي بعد 7؟', ['6', '7', '8', '9'], '8', 'math'),
            $this->question('كم عدد أصابع يد الطفل الواحدة؟', ['4', '5', '6', '7'], '5', 'math'),
            $this->question('أي عدد أكبر: 14 أم 11؟', ['14', '11', '12', '13'], '14', 'math'),
            $this->question('ما الناتج: 2 + 3؟', ['4', '5', '6', '7'], '5', 'math'),
            $this->question('ما الناتج: 6 - 2؟', ['2', '3', '4', '5'], '4', 'math'),
            $this->question('ما العدد الناقص في النمط: 1، 3، 5، __؟', ['6', '7', '8', '9'], '7', 'math'),
            $this->question('ما الناتج: 4 + 4؟', ['6', '7', '8', '9'], '8', 'math'),
            $this->question('أي عدد أصغر: 8 أم 5؟', ['8', '5', '6', '7'], '5', 'math'),
            $this->question('ما العدد التالي في النمط: 10، 20، 30، __؟', ['35', '40', '45', '50'], '40', 'math'),
        ];
    }

    private function dyslexiaTrainingLevelOne(): array
    {
        return [
            $this->question('اختر الحرف الصحيح في كلمة "باب"', ['ب', 'ت', 'ث', 'ج'], 'ب', 'letter'),
            $this->question('ما الحرف الأول في كلمة "قلم"', ['ق', 'ل', 'م', 'ن'], 'ق', 'letter'),
            $this->question('ما الحرف الأخير في كلمة "نور"', ['ن', 'و', 'ر', 'م'], 'ر', 'letter'),
            $this->question('اختر الحرف الصحيح في كلمة "بيت"', ['ب', 'ت', 'ث', 'ط'], 'ب', 'letter'),
            $this->question('ما الحرف الأوسط في كلمة "سمك"', ['س', 'م', 'ك', 'ن'], 'م', 'letter'),
            $this->question('أي حرف تبدأ به كلمة "فم"؟', ['ف', 'م', 'ن', 'ب'], 'ف', 'letter'),
            $this->question('أي حرف تنتهي به كلمة "شجرة"؟', ['ة', 'ر', 'ش', 'ج'], 'ة', 'letter'),
            $this->question('اختر الحرف الصحيح في كلمة "طير"', ['ط', 'ت', 'ظ', 'ض'], 'ط', 'letter'),
            $this->question('ما الحرف الأول في كلمة "ذهب"', ['ذ', 'ز', 'د', 'ر'], 'ذ', 'letter'),
            $this->question('ما الحرف الأول في كلمة "كتاب"', ['ك', 'ت', 'ا', 'ب'], 'ك', 'letter'),
        ];
    }

    private function dyslexiaTrainingLevelTwo(): array
    {
        return [
            $this->question('أكمل الكلمة: م_درسة', ['د', 'ر', 'س', 'ة'], 'د', 'word'),
            $this->question('اختر الكلمة الصحيحة: "وردة"', ['وردة', 'ورده', 'وردا', 'وردت'], 'وردة', 'word'),
            $this->question('ما الحرف الأوسط في كلمة "قلم"؟', ['ل', 'ق', 'م', 'ب'], 'ل', 'word'),
            $this->question('أي كلمة تبدأ بحرف "ث"؟', ['ثوب', 'بيت', 'قلم', 'شمس'], 'ثوب', 'word'),
            $this->question('ما الحرف الأخير في كلمة "مدرسة"؟', ['ة', 'م', 'س', 'د'], 'ة', 'word'),
            $this->question('أي كلمة فيها حرف "ف"؟', ['فراشة', 'شجرة', 'بيت', 'قمر'], 'فراشة', 'word'),
            $this->question('أي كلمة فيها ثلاثة أحرف؟', ['بيت', 'مدرسة', 'شجرة', 'تفاحة'], 'بيت', 'word'),
            $this->question('اختر الكلمة الصحيحة: "طفل"', ['طفل', 'طفيل', 'طفلٌ', 'طفي'], 'طفل', 'word'),
            $this->question('أي كلمة تبدأ بحرف "ص"؟', ['صقر', 'قمر', 'بيت', 'شمس'], 'صقر', 'word'),
            $this->question('أي كلمة تعني مكان التعلم؟', ['مدرسة', 'سيارة', 'قمر', 'تفاحة'], 'مدرسة', 'word'),
        ];
    }

    private function dyslexiaTrainingLevelThree(): array
    {
        return [
            $this->question('اختر الكلمة الصحيحة: "شجرة"', ['شجرة', 'شجره', 'شجيرة', 'شجرات'], 'شجرة', 'reading'),
            $this->question('ما الحرف الأول في كلمة "مدرسة"؟', ['م', 'د', 'ر', 'س'], 'م', 'reading'),
            $this->question('أي كلمة فيها حرف "ع"؟', ['عصفور', 'قمر', 'بيت', 'شمس'], 'عصفور', 'reading'),
            $this->question('أكمل الكلمة: فرا_ة', ['ش', 'س', 'ز', 'ب'], 'ش', 'reading'),
            $this->question('ما الحرف الأخير في كلمة "طائرة"؟', ['ة', 'ر', 'ط', 'ا'], 'ة', 'reading'),
            $this->question('أي كلمة تدل على مكان الدراسة؟', ['مدرسة', 'سيارة', 'طائرة', 'شجرة'], 'مدرسة', 'reading'),
            $this->question('أي كلمة تبدأ بحرف "خ"؟', ['خيمة', 'بيت', 'قمر', 'ورد'], 'خيمة', 'reading'),
            $this->question('أي كلمة فيها حرف "ذ"؟', ['ذهب', 'باب', 'قلم', 'شمس'], 'ذهب', 'reading'),
            $this->question('أي كلمة فيها أربعة أحرف؟', ['قمر', 'نور', 'كتاب', 'بيت'], 'كتاب', 'reading'),
            $this->question('أي كلمة نستخدمها لقراءة القصص؟', ['كتاب', 'قلم', 'باب', 'طاولة'], 'كتاب', 'reading'),
        ];
    }

    private function dyscalculiaTrainingLevelOne(): array
    {
        return [
            $this->question('ما العدد التالي بعد 1؟', ['2', '3', '4', '5'], '2', 'number'),
            $this->question('كم عدد أصابع اليد الواحدة؟', ['4', '5', '6', '7'], '5', 'number'),
            $this->question('أي عدد أكبر: 4 أم 7؟', ['4', '7', '5', '6'], '7', 'number'),
            $this->question('أي عدد أصغر: 9 أم 6؟', ['9', '6', '7', '8'], '6', 'number'),
            $this->question('ما الناتج: 1 + 2؟', ['2', '3', '4', '5'], '3', 'number'),
            $this->question('ما الناتج: 5 - 1؟', ['2', '3', '4', '5'], '4', 'number'),
            $this->question('ما العدد الناقص في النمط: 2، 3، __، 5؟', ['4', '5', '6', '3'], '4', 'number'),
            $this->question('أي رقم يمثل العدد خمسة؟', ['3', '4', '5', '6'], '5', 'number'),
            $this->question('ما العدد التالي بعد 14؟', ['13', '14', '15', '16'], '15', 'number'),
            $this->question('ما الناتج: 6 - 3؟', ['1', '2', '3', '4'], '3', 'number'),
        ];
    }

    private function dyscalculiaTrainingLevelTwo(): array
    {
        return [
            $this->question('ما العدد التالي في النمط: 2، 4، 6، __؟', ['7', '8', '9', '10'], '8', 'number'),
            $this->question('ما الناتج: 10 + 1؟', ['10', '11', '12', '13'], '11', 'number'),
            $this->question('ما الناتج: 9 - 3؟', ['5', '6', '7', '8'], '6', 'number'),
            $this->question('أي عدد أكبر: 13 أم 15؟', ['13', '14', '15', '16'], '15', 'number'),
            $this->question('ما الناتج: 7 + 2؟', ['8', '9', '10', '11'], '9', 'number'),
            $this->question('ما الناتج: 8 - 5؟', ['1', '2', '3', '4'], '3', 'number'),
            $this->question('ما العدد التالي في النمط: 5، 10، 15، __؟', ['18', '19', '20', '25'], '20', 'number'),
            $this->question('ما العدد التالي بعد 19؟', ['18', '19', '20', '21'], '20', 'number'),
            $this->question('ما العدد التالي في النمط: 6، 7، 8، __؟', ['8', '9', '10', '11'], '9', 'number'),
            $this->question('ما الناتج: 2 + 6؟', ['6', '7', '8', '9'], '8', 'number'),
        ];
    }

    private function dyscalculiaTrainingLevelThree(): array
    {
        return [
            $this->question('ما الناتج: 12 + 3؟', ['14', '15', '16', '17'], '15', 'number'),
            $this->question('ما الناتج: 18 - 4؟', ['12', '13', '14', '15'], '14', 'number'),
            $this->question('أي عدد أكبر: 24 أم 42؟', ['24', '42', '26', '40'], '42', 'number'),
            $this->question('ما الناتج: 7 + 6؟', ['11', '12', '13', '14'], '13', 'number'),
            $this->question('ما الناتج: 20 - 9؟', ['9', '10', '11', '12'], '11', 'number'),
            $this->question('ما العدد التالي في النمط: 3، 6، 9، __؟', ['10', '11', '12', '15'], '12', 'number'),
            $this->question('ما الناتج: 14 + 2؟', ['15', '16', '17', '18'], '16', 'number'),
            $this->question('ما الناتج: 30 - 5؟', ['20', '22', '25', '26'], '25', 'number'),
            $this->question('أي عدد أصغر: 27 أم 29؟', ['27', '28', '29', '30'], '27', 'number'),
            $this->question('ما العدد التالي في النمط: 2، 4، 8، __؟', ['12', '14', '16', '10'], '16', 'number'),
        ];
    }
}
