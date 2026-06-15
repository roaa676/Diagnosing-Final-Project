# 📋 Component Connection Checklist - قائمة الربط للـ Components

## 📊 حالة الربط الحالية

### ✅ مربوطة (Connected)
- [ ] **Login Component** ← AuthService
- [ ] **Register Component** ← AuthService
- [ ] **Chatbot Component** ← ChatbotService (جزئياً)

### ⏳ قيد الربط (In Progress)
قائمة الـ Components التي تحتاج ربط:

---

## 🔗 Components التي تحتاج ربط

### 1. **Profile Component**
**الملف:** `src/app/pages/profile/`

**الخدمات المطلوبة:**
- [ ] ProfileService - `getProfile()`
- [ ] ProfileService - `updateProfile()`
- [ ] ProfileService - `changePassword()`
- [ ] ProfileService - `uploadProfileImage()`
- [ ] ChildService - `getAllChildren()`
- [ ] ChildService - `createChild()`
- [ ] ChildService - `uploadChildImage()`

**الـ APIs المستخدمة:**
```
GET    /api/user/profile
PUT    /api/user/profile/update
PUT    /api/user/profile/password
POST   /api/user/upload-image
GET    /api/children
POST   /api/children
POST   /api/child/{id}/upload-image
```

**Todo:**
- [ ] عرض بيانات المستخدم
- [ ] تحديث البيانات
- [ ] تغيير كلمة السر
- [ ] رفع صورة الملف الشخصي
- [ ] إضافة طفل جديد
- [ ] عرض قائمة الأطفال
- [ ] رفع صور الأطفال

---

### 2. **Dashboard Component**
**الملف:** `src/app/pages/Dashboard/`

**الخدمات المطلوبة:**
- [ ] AdminService - `getStats()`
- [ ] ChildService - `getAllChildren()`
- [ ] HistoryService - `getChildReport()`

**الـ APIs المستخدمة:**
```
GET    /api/admin/stats
GET    /api/children
GET    /api/child/{id}/report
```

**Todo:**
- [ ] عرض الإحصائيات
- [ ] عرض الأطفال
- [ ] عرض ملخص التقدم
- [ ] عرض المخططات (Charts)

---

### 3. **Learning-Difficulties Component**
**الملف:** `src/app/pages/Learning-difficulties/`

**الخدمات المطلوبة:**
- [ ] LearningDifficultyService - `getAllDifficulties()`
- [ ] LearningDifficultyService - `getDifficultyQuestions()`
- [ ] AssessmentService - `getAssessmentContent()`
- [ ] AssessmentService - `submitQuestionnaire()`
- [ ] AssessmentService - `getChildResults()`

**الـ APIs المستخدمة:**
```
GET    /api/difficulties
GET    /api/difficulties/{id}/questions
GET    /api/assessment-content/{id}
POST   /api/submit-questionnaire
GET    /api/results/{child_id}
```

**Todo:**
- [ ] عرض قائمة الصعوبات
- [ ] عرض تفاصيل كل صعوبة
- [ ] بدء استبيان التقييم
- [ ] عرض الأسئلة
- [ ] تقديم الإجابات
- [ ] عرض النتائج

---

### 4. **Training Component**
**الملف:** `src/app/pages/Training/`

**الخدمات المطلوبة:**
- [ ] TrainingService - `getTrainingRoadmap()`
- [ ] TrainingService - `getGameContent()`
- [ ] TrainingService - `submitGameResult()`
- [ ] TrainingService - `completeTrainingLevel()`

**الـ APIs المستخدمة:**
```
GET    /api/training/roadmap/{child_id}
GET    /api/game-content/{difficulty}/{level}
POST   /api/submit-game-result
POST   /api/training/complete
```

**Todo:**
- [ ] عرض مسار التدريب
- [ ] تحميل ألعاب التدريب
- [ ] تقديم نتائج اللعبة
- [ ] إكمال مستوى
- [ ] عرض التقدم

---

### 5. **Training-Levels Component**
**الملف:** `src/app/pages/Training-Levels/`

**الخدمات المطلوبة:**
- [ ] TrainingService - `getTrainingRoadmap()`
- [ ] TrainingService - `getGameContent()`
- [ ] TrainingService - `completeTrainingLevel()`

**الـ APIs المستخدمة:**
```
GET    /api/training/roadmap/{child_id}
GET    /api/game-content/{difficulty}/{level}
POST   /api/training/complete
```

**Todo:**
- [ ] عرض مستويات التدريب
- [ ] تحديث حالة المستويات
- [ ] الانتقال بين المستويات

---

### 6. **History Component**
**الملف:** `src/app/pages/history/`

**الخدمات المطلوبة:**
- [ ] HistoryService - `getChildHistory()`
- [ ] HistoryService - `getChildReport()`

**الـ APIs المستخدمة:**
```
GET    /api/child/{child_id}/history
GET    /api/child/{child_id}/report
```

**Todo:**
- [ ] عرض السجل الكامل
- [ ] عرض التقرير الشامل
- [ ] عرض الإحصائيات والمخططات

---

### 7. **Chatbot Component** (جزئياً مربوطة)
**الملف:** `src/app/pages/chatbot/`

**الخدمات المطلوبة:**
- [x] ChatbotService - `ask()` ← موجودة بالفعل
- [ ] ChatbotService - `explainResult()`
- [ ] ChatbotService - `recommendExercises()`

**الـ APIs المستخدمة:**
```
POST   /api/chatbot/ask
POST   /api/chatbot/explain-result
POST   /api/chatbot/recommend-exercises
```

**Todo:**
- [x] طرح الأسئلة (موجود)
- [ ] شرح النتائج
- [ ] الحصول على توصيات تمرينية

---

## 🔄 خطوات الربط

### الخطوة 1: استيراد الخدمة
```typescript
import { ProfileService } from '@core/services/profile.service';
```

### الخطوة 2: إضافتها للـ Constructor
```typescript
constructor(private profileService: ProfileService) {}
```

### الخطوة 3: استدعاء الـ Method
```typescript
ngOnInit() {
  this.profileService.getProfile().subscribe({
    next: (response) => {
      this.userData = response.user;
    },
    error: (error) => {
      console.error('Error loading profile:', error);
    }
  });
}
```

### الخطوة 4: عرض البيانات في Template
```html
<div *ngIf="userData">
  <h1>{{ userData.name }}</h1>
  <p>{{ userData.email }}</p>
</div>
```

---

## 📝 Example: ربط Profile Component بالكامل

### TypeScript
```typescript
import { Component, OnInit } from '@angular/core';
import { ProfileService } from '@core/services/profile.service';
import { ChildService } from '@core/services/child.service';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css']
})
export class ProfileComponent implements OnInit {
  userData: any;
  children: any[] = [];
  loading = false;

  constructor(
    private profileService: ProfileService,
    private childService: ChildService
  ) {}

  ngOnInit() {
    this.loadProfile();
    this.loadChildren();
  }

  loadProfile() {
    this.loading = true;
    this.profileService.getProfile().subscribe({
      next: (response) => {
        this.userData = response.user;
        this.loading = false;
      },
      error: (error) => {
        console.error('Error loading profile:', error);
        this.loading = false;
      }
    });
  }

  loadChildren() {
    this.childService.getAllChildren().subscribe({
      next: (response) => {
        this.children = response.children || [];
      },
      error: (error) => console.error('Error loading children:', error)
    });
  }

  updateProfile(data: any) {
    this.profileService.updateProfile(data).subscribe({
      next: (response) => {
        console.log('Profile updated:', response);
        this.loadProfile();
      },
      error: (error) => console.error('Error updating profile:', error)
    });
  }

  addChild(name: string, age: number) {
    this.childService.createChild(name, age).subscribe({
      next: (response) => {
        console.log('Child added:', response);
        this.loadChildren();
      },
      error: (error) => console.error('Error adding child:', error)
    });
  }
}
```

### HTML
```html
<div *ngIf="loading" class="loader">جاري التحميل...</div>

<div *ngIf="userData" class="profile-section">
  <h2>{{ userData.name }}</h2>
  <p>{{ userData.email }}</p>
  
  <form (ngSubmit)="updateProfile(profileForm.value)" #profileForm="ngForm">
    <input [(ngModel)]="userData.name" name="name" placeholder="الاسم">
    <input [(ngModel)]="userData.phone" name="phone" placeholder="رقم الهاتف">
    <button type="submit">تحديث</button>
  </form>
</div>

<div class="children-section">
  <h3>الأطفال</h3>
  <div *ngFor="let child of children" class="child-item">
    <p>{{ child.name }} - {{ child.age }} سنة</p>
  </div>
  
  <form (ngSubmit)="addChild(childName.value, childAge.value)">
    <input #childName placeholder="اسم الطفل">
    <input #childAge type="number" placeholder="العمر">
    <button type="submit">إضافة</button>
  </form>
</div>
```

---

## ✅ Priority Order - ترتيب الأولويات

1. **High Priority** 🔴
   - [ ] Profile Component
   - [ ] Dashboard Component
   - [ ] Learning-Difficulties Component

2. **Medium Priority** 🟡
   - [ ] Training Component
   - [ ] History Component
   - [ ] Chatbot Complete

3. **Low Priority** 🟢
   - [ ] Training-Levels Component
   - [ ] Admin Components

---

## 📊 Progress Tracker

| Component | Status | Completed % |
|-----------|--------|------------|
| Profile | ⏳ Not Started | 0% |
| Dashboard | ⏳ Not Started | 0% |
| Learning Difficulties | ⏳ Not Started | 0% |
| Training | ⏳ Not Started | 0% |
| History | ⏳ Not Started | 0% |
| Chatbot | 🟡 Partial | 33% |
| Training-Levels | ⏳ Not Started | 0% |

---

## 💡 Tips for Connection

✅ استخدم `async pipe` للبيانات
✅ أضف `*ngIf` للتحقق من وجود البيانات
✅ أضف `Loading states`
✅ أضف `Error messages`
✅ استخدم `unsubscribe` في `OnDestroy`

---

**ابدأ بـ Profile Component أولاً، فهو الأبسط! 🚀**
