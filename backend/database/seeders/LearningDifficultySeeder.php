<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LearningDifficultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    \App\Models\LearningDifficulty::create([
        'name_ar' => 'عسر القراءة',
        'name_en' => 'Dyslexia',
        'description' => 'صعوبة تعليمية محددة تؤثر بشكل أساسي على المهارات المرتبطة بالقراءة والتهجئة بدقة وطلاقة.',
        'symptoms' => ['القراءة ببطء وصعوبة', 'الخلط بين الحروف المتشابهة شكلاً', 'صعوبة في تذكر تسلسل الحروف'],
        'parent_advice' => 'الصبر هو المفتاح، خصص 20 دقيقة يومياً للقراءة المشتركة دون ضغط.',
        'icon' => 'book-icon'
    ]);

    \App\Models\LearningDifficulty::create([
        'name_ar' => 'عسر الحساب',
        'name_en' => 'Dyscalculia',
        'description' => 'صعوبة في فهم الأرقام وتعلم الحقائق الرياضية، يجد الطفل تحدياً في التعامل مع الكميات والعمليات.',
        'symptoms' => ['صعوبة في عد النقود', 'نسيان القواعد الحسابية الأساسية', 'صعوبة في تقدير المسافات'],
        'parent_advice' => 'استخدم الأشياء الملموسة مثل الفاكهة أو المكعبات لشرح المفاهيم الحسابية بدلاً من الورقة والقلم فقط.',
        'icon' => 'math-icon'
    ]);
}
}
