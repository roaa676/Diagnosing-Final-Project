# 📋 شاشات الاستبيان والتقييم - دليل الاستخدام

## 🎯 نظرة عامة

تم تنفيذ شاشتين رئيسيتين:

1. **شاشة الاستبيان** (Questionnaire) - استبيان ولي الأمر
2. **شاشة التقييم** (Assessment) - تقييم قدرات الطفل

---

## 📍 المسارات

```
/questionnaire     → شاشة الاستبيان
/assessment        → شاشة التقييم
/training          → التدريب/اللعب (القادم)
```

---

## 🔗 الربط من صفحات أخرى

### من صفحة Learning Difficulties

```typescript
import { Router } from '@angular/router';

// في component الخاص بك
constructor(private router: Router) {}

selectDifficulty(childId: number, difficultyId: number) {
  this.router.navigate(['/questionnaire'], {
    queryParams: {
      childId: childId,
      difficultyId: difficultyId
    }
  });
}
```

### من Dashboard

```typescript
// بعد اختيار الطفل
navigateToQuestionnaire() {
  this.router.navigate(['/questionnaire'], {
    queryParams: {
      childId: this.selectedChild.id,
      difficultyId: 1  // أو أي صعوبة أخرى
    }
  });
}
```

---

## 📊 سير العملية

```
Dashboard / Learning Difficulties
        ↓
  اختيار الطفل وصعوبة التعلم
        ↓
    /questionnaire
  (استبيان 4 أسئلة)
        ↓
   إرسال الإجابات
        ↓
   رسالة نجاح
        ↓
    /assessment
  (تقييم متعدد الأسئلة)
        ↓
   عرض النتائج
        ↓
    /training
  (تدريبات للطفل)
```

---

## 🎨 المميزات

### شاشة الاستبيان

✅ **التصميم الجميل**
- ألوان أخضر موحد
- تدرج خلفية فاتح
- أيقونات وتنبيهات

✅ **الوظائف**
- 4 أسئلة قياسية
- نظام تقدم حي
- خيارات راديو واضحة
- زر تحذير برتقالي
- معالجة الأخطاء

✅ **الاستجابة**
- تصميم responsive
- يعمل على الهاتف والويب

---

### شاشة التقييم

✅ **الميزات**
- تحميل الأسئلة من الـ API
- نظام توقيت لكل سؤال
- عداد النقاط المباشر
- رسائل توضيحية

✅ **الفعاليات**
- تقدم الأسئلة
- تخطي السؤال
- عرض النتائج

✅ **التفاعل**
- رسالة نجاح ودية
- زر الانتقال للتدريب

---

## 🔧 الاستخدام من الكود

### استيراد المكونات

```typescript
import { QuestionnaireComponent } from '@/pages/questionnaire/questionnaire.component';
import { AssessmentComponent } from '@/pages/assessment/assessment.component';
```

### في الـ Routes

```typescript
{ path: 'questionnaire', component: QuestionnaireComponent },
{ path: 'assessment', component: AssessmentComponent }
```

---

## 🧪 الاختبار اليدوي

### 1. الانتقال للاستبيان
```
http://localhost:4200/questionnaire?childId=1&difficultyId=1
```

### 2. الإجابة على الأسئلة
- اختر إجابة لكل سؤال
- راقب شريط التقدم
- اضغط "إرسال الإجابات"

### 3. رسالة النجاح
- اضغط "الانتقال لتقييم الطفل"

### 4. التقييم
```
http://localhost:4200/assessment?childId=1&difficultyId=1
```

- أجب على الأسئلة في الوقت المحدد
- اضغط "الإجابة" أو "تخطي"
- شاهد النتائج النهائية

---

## 📡 البيانات المرسلة/المستقبلة

### Request إلى `/submit-questionnaire`
```json
{
  "child_id": 1,
  "learning_difficulty_id": 1,
  "q1_reading_aloud": 1,
  "q2_confusing_letters": 2,
  "q3_forgetting_instructions": 1,
  "q4_avoiding_reading": 2
}
```

### Response من `/submit-questionnaire`
```json
{
  "status": "success",
  "message": "تم حفظ التقييم بنجاح",
  "risk_level": "high",
  "recommendation": {
    "training_level": 1,
    "message": "يحتاج الطفل إلى تدريب مكثف"
  },
  "data": {
    "id": 15,
    "total_risk_score": 6,
    "risk_level": "high"
  }
}
```

---

## ⚠️ الملاحظات

1. **يجب تمرير childId و difficultyId** عند الانتقال
2. **الخادم يجب أن يكون يعمل** على `http://127.0.0.1:8000`
3. **التوكن يجب أن يكون موجود** في localStorage (يضاف تلقائياً)

---

## 🔮 الخطوات التالية

- [ ] تنفيذ شاشة التدريب (Game Screen)
- [ ] ربط Dashboard للعرض الأول
- [ ] إضافة loading states محسّنة
- [ ] اختبار التدفق الكامل

