# 🚀 Quick Start Guide - دليل البدء السريع

## 5 خطوات للبدء

### 1️⃣ **شغل Backend**
```bash
cd backend
composer install  # إذا لم تكن مثبتة
php artisan migrate
php artisan serve
```
✅ السيرفر سيعمل على: `http://127.0.0.1:8000`

---

### 2️⃣ **شغل Frontend**
```bash
# في مجلد جديد
npm start
# أو
ng serve
```
✅ الـ Frontend سيعمل على: `http://localhost:4200`

---

### 3️⃣ **استخدم الـ Services في Component**

#### مثال: تحميل الأطفال
```typescript
import { Component, OnInit } from '@angular/core';
import { ChildService } from '@core/services/child.service';

@Component({
  selector: 'app-children',
  template: `<button (click)="loadChildren()">Load</button>`
})
export class ChildrenComponent implements OnInit {
  children: any[] = [];

  constructor(private childService: ChildService) {}

  ngOnInit() {
    this.loadChildren();
  }

  loadChildren() {
    this.childService.getAllChildren().subscribe({
      next: (res) => {
        this.children = res.children || [];
        console.log('Children loaded:', this.children);
      },
      error: (err) => console.error('Error:', err)
    });
  }
}
```

---

### 4️⃣ **Services المتاحة**

```typescript
// تسجيل الدخول
import { AuthService } from '@core/services/auth.service';
authService.login(email, password);

// إدارة الأطفال
import { ChildService } from '@core/services/child.service';
childService.getAllChildren();
childService.createChild(name, age);

// الملف الشخصي
import { ProfileService } from '@core/services/profile.service';
profileService.getProfile();
profileService.updateProfile(data);

// التقييمات
import { AssessmentService } from '@core/services/assessment.service';
assessmentService.getAssessmentContent(difficultyId);
assessmentService.submitQuestionnaire(data);

// التدريبات
import { TrainingService } from '@core/services/training.service';
trainingService.getTrainingRoadmap(childId);
trainingService.getGameContent(difficultyId, level);
trainingService.submitGameResult(childId, gameType, score);

// السجل والتقارير
import { HistoryService } from '@core/services/history.service';
historyService.getChildHistory(childId);
historyService.getChildReport(childId);

// الصعوبات التعليمية
import { LearningDifficultyService } from '@core/services/learning-difficulty.service';
learningDifficultyService.getAllDifficulties();

// المساعد الذكي
import { ChatbotService } from '@core/services/chatbot.service';
chatbotService.ask(message, childId);

// لوحة التحكم (Admin)
import { AdminService } from '@core/services/admin.service';
adminService.getStats();
adminService.getAllQuestions();
```

---

### 5️⃣ **اختبر مع Postman**

```json
1. POST /api/login
   {"email": "ahmed@example.com", "password": "password123"}

2. GET /api/user/profile
   Headers: Authorization: Bearer {token}

3. GET /api/children
   Headers: Authorization: Bearer {token}

4. GET /api/difficulties
   (بدون token)
```

---

## 📌 الملفات المهمة

| الملف | الوصف |
|-------|--------|
| `API_INTEGRATION_GUIDE.md` | خريطة الربط بين Components والـ APIs |
| `COMPLETE_API_REFERENCE.md` | توثيق شامل لكل API |
| `INTEGRATION_SUMMARY.md` | ملخص الحالة والخطوات التالية |

---

## ⚡ نصائح سريعة

✅ **التوكن يُضاف تلقائياً** - لا تقلق من إضافته يدويًا

✅ **الأخطاء تُعالج تلقائياً** - 401 يوجهك لصفحة Login

✅ **استخدم RxJS** - subscribe للحصول على البيانات

✅ **احفظ التوكن** - يُحفظ في localStorage تلقائياً

---

## 🎯 أكثر الـ APIs استخداماً

```typescript
// 1. تسجيل الدخول
authService.login('email@example.com', 'password').subscribe(res => {
  console.log('Token:', res.token);
});

// 2. تحميل الأطفال
childService.getAllChildren().subscribe(res => {
  this.children = res.children;
});

// 3. بدء تقييم
assessmentService.getAssessmentContent(1).subscribe(res => {
  this.questions = res.data.questions;
});

// 4. تقديم نتيجة لعبة
trainingService.submitGameResult(1, 'visual_discrimination', 85).subscribe(res => {
  console.log('Score saved:', res);
});

// 5. الحصول على التقرير
historyService.getChildReport(1).subscribe(res => {
  this.report = res.data;
});
```

---

## ❌ الأخطاء الشائعة

### ❌ خطأ 1: Backend لم يكن يعمل
```bash
# الحل: تأكد من تشغيل السيرفر
php artisan serve
```

### ❌ خطأ 2: 401 Unauthorized
```typescript
// الحل: تأكد من تسجيل الدخول أولاً
authService.login(email, password).subscribe(...);
```

### ❌ خطأ 3: CORS Error
```typescript
// الحل: جرب مع Postman أولاً
// إذا عملت مع Postman، فالمشكلة في الـ Frontend config
```

### ❌ خطأ 4: لا توجد بيانات
```typescript
// الحل: تحقق من الـ console والـ Network tab
console.log(response);  // اطبع البيانات
```

---

## ✅ ماذا بعد؟

### للـ Frontend Developers
1. استخدم الـ Services في Components
2. أظهر البيانات في UI
3. أضف Loading states
4. أضف Error messages

### للـ Backend Developers
1. اختبر الـ APIs مع Postman
2. أضف Validation
3. أضف Logging
4. اختبر الأداء

---

## 💬 المساعدة

**تحتاج مساعدة؟**
- 📄 اقرأ `COMPLETE_API_REFERENCE.md`
- 🧪 اختبر مع Postman
- 🔍 اطبع البيانات في Console
- 📞 اطلب مساعدة

---

**أنت الآن جاهز للبدء! 🎉**
