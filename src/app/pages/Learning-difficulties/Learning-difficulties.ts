import { Component } from '@angular/core';
import { CommonModule } from '@angular/common'; 

@Component({
  selector: 'app-learning-difficulties',
  standalone: true, 
  imports: [CommonModule], 
 templateUrl: './Learning-difficulties.html',
  styleUrls: ['./learning-difficulties.css']
})
export class LearningDifficultiesComponent {
  
  
  difficulties = [
    {
      title: 'عسر القراءة',
      latin: 'Dyslexia',
      icon: 'fa-book-open',
      theme: 'green-theme',
      imagePath: 'assets/images/Dyslexia.png',
      desc: 'صعوبة تعليمية محددة تؤثر بشكل أساسي على المهارات <br> المرتبطة بالقراءة والتهجئة بدقة وطلاقة. لا ترتبط بالذكاء، <br> بل بطريقة معالجة الدماغ للغة.',
      symptoms: ['القراءة ببطء وصعوبة', 'الخلط بين الحروف المتشابهة', 'صعوبة في تذكر تسلسل الحروف'],
      trainings: ['لعبة صيد الحروف', 'القصص المسموعة'],
      parentTip:  'الصبر هو المفتاح. خصص 20  دقيقة  يومياً  للقراءة <br>   المشتركة دون ضغط أو تصحيح مستمر للأخطاء لتعزيز<br>   الثقة.'    },
    {
      title: 'عسر الحساب',
      latin: 'Dyscalculia',
      icon: 'fa-calculator',
      theme: 'purple-theme',
      imagePath: 'assets/images/Dyscalculia.png',
      desc: 'صعوبة في فهم الأرقام وتعلم الحقائق الرياضية. <br> يجد الطفل تحدياً في التعامل مع الكميات، الوقت، <br> والعمليات الحسابية البسيطة.',
      symptoms: ['صعوبة في عد النقود أو معرفة الوقت', 'نسيان القواعد الحسابية الأساسية', 'صعوبة في تقدير المسافات والكميات'],
      trainings: ['السوق المنزلي', 'ألعاب الدومينو'],
parentTip: 'استخدم الأشياء الملموسة (مثل الفواكه أو الألعاب)  <br>  لشرح المفاهيم الحسابية بدلا من الورقة والقلم .'    },
    {
      title: 'عسر الكتابة',
      latin: 'Dysgraphia',
      icon: 'fa-solid fa-pen',
      theme: 'pink-theme',
      imagePath: 'assets/images/Dysgraphia.png',
      desc: 'خلل يؤثر على القدرة على الكتابة اليدوية والتهجئة <br> وتنظيم الأفكار كتابياً؛ قد تكون الكتابة غير <br> مقروءة أو مؤلمة جسدياً للطفل.',
      symptoms: ['خط سيء جداً وغير مقروء', 'مسك القلم بقوة مفرطة تسبب التعب', 'كثرة الأخطاء الإملائية والكلمات المحذوفة'],
      trainings: ['الرسم على الرمل', 'تتبع الخطوط'],
      parentTip: 'شجع استخدام الوسائل البديل مثل الكتابة على لوحة <br>  المفاتيح أو التسجيل الصوتي لتقليل الاحباط أثناء أداء<br>  الواجبات.'
 }
 ];

}

  [
  {
    title: 'عسر القراءة',
    imagePath: 'assets/images/Dyslexia.png.png', 
  },
  {
    title: 'عسر الحساب',
    imagePath: 'assets/images/Dyscalculia.png',
    
  },
  {
    title: 'عسر الكتابة',
    imagePath: 'assets/images/Dysgraphia.png',
    
  }
];