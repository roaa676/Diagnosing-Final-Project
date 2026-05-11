import { Component } from '@angular/core';
// 1. استيراد CommonModule عشان الـ *ngFor والـ ngClass يشتغلوا
import { CommonModule } from '@angular/common'; 

@Component({
  selector: 'app-learning-difficulties',
  standalone: true, // تأكدي إن دي موجودة لو إنتي شغالة بالإصدارات الجديدة
  imports: [CommonModule], // 2. إضافة CommonModule هنا في مصفوفة الـ imports
  templateUrl: './learning_difficulties.html',
  styleUrls: ['./learning_difficulties.css']
})
export class LearningDifficultiesComponent {
  // ... باقي مصفوفة الـ difficulties اللي بعتهالك قبل كدة ...
  difficulties = [
    {
      title: 'عسر القراءة',
      latin: 'Dyslexia',
      icon: 'fa-book-open',
      theme: 'green-theme',
      desc: 'صعوبة تعليمية محددة تؤثر بشكل أساسي على المهارات <br> المرتبطة بالقراءة والتهجئة بدقة وطلاقة. لا ترتبط بالذكاء، <br> بل بطريقة معالجة الدماغ للغة.',   
      symptoms:['القراءة ببطء وصعوبة', 'الخلط بين الحروف المتشابهة', 'صعوبة في تذكر تسلسل الحروف'],
      trainings: ['لعبة صيد الحروف ','القصص المسموعة'],
      parentTip: 'الصبر هو المفتاح، خصص 20 دقيقة يومياً للقراءة المشتركة دون ضغط أو تصحيح مستمر للأخطاء.'
    },
    {
      title: 'عسر الحساب',
      latin: 'Dyscalculia',
      icon: 'fa-calculator',
      theme: 'purple-theme',
      desc: 'صعوبة في فهم الأرقام وتعلم الحقائق الرياضية. يجد  <br> الطفل تحديًا في التعامل مع الكميات،الوقت،والعمليات <br>الحسابية البسيطة.',
      symptoms: ['صعوبة في عد النقود أو معرفة الوقت', 'نسيان القواعد الحسابية الأساسية', 'صعوبة في تقدير المسافات والكميات'],
      trainings: ['السوق المنزلي', 'ألعاب الدومينو'],
      parentTip: 'استخدم الأشياء الملموسة (مثل الفواكه أو الألعاب) لشرح المفاهيم الحسابية بدلاً من الورقة والقلم.'
    },
    {
      title: 'عسر الكتابة',
      latin: 'Dysgraphia',
      icon: 'fa-pen-nib',
      theme: 'pink-theme',
      desc: 'خلل يؤثر على القدرة على الكتابة اليدوية والتهجئة وتنظيم الأفكار كتابياً؛ قد تكون الكتابة غير مقروءة أو مؤلمة جسدياً للطفل.',
      symptoms: ['خط سيء جداً وغير مقروء', 'مسك القلم بقوة مفرطة تسبب التعب', 'كثرة الأخطاء الإملائية والكلمات المحذوفة'],
      trainings: ['الرسم على الرمل', 'تتبع الخطوط'],
      parentTip: 'شجع استخدام الوسائل البديلة مثل الكتابة على الكيبورد أو التسجيل الصوتي لتقليل الإحباط.'
    }
  ];
}