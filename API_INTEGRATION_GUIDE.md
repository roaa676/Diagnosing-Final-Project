# 🔗 API Integration Guide - الدليل الشامل للربط

## ✅ الخدمات المُنشأة (Services Created)

### 1. **AuthService** ✓ (موجودة)
- ✅ `login(email, password)` → POST `/api/login`
- ✅ `register(...)` → POST `/api/register`
- ✅ `logout()` → محلي (local storage)
- ✅ `getToken()` → الحصول على التوكن
- ✅ `isAuthenticated()` → التحقق من تسجيل الدخول

---

### 2. **ChatbotService** ✓ (موجودة)
- ✅ `ask(message, childId)` → POST `/api/chatbot/ask`
- ✅ `explainResult(childId, resultId)` → POST `/api/chatbot/explain-result`
- ✅ `recommendExercises(childId)` → POST `/api/chatbot/recommend-exercises`

---

### 3. **ChildService** ✨ (جديدة)
- ✅ `getAllChildren()` → GET `/api/children`
- ✅ `createChild(name, age)` → POST `/api/children`
- ✅ `uploadChildImage(childId, file)` → POST `/api/child/{childId}/upload-image`

---

### 4. **ProfileService** ✨ (جديدة)
- ✅ `getProfile()` → GET `/api/user/profile`
- ✅ `updateProfile(userData)` → PUT `/api/user/profile/update`
- ✅ `changePassword(passwordData)` → PUT `/api/user/profile/password`
- ✅ `uploadProfileImage(file)` → POST `/api/user/upload-image`

---

### 5. **AssessmentService** ✨ (جديدة)
- ✅ `getAssessmentContent(difficultyId)` → GET `/api/assessment-content/{difficultyId}`
- ✅ `submitQuestionnaire(data)` → POST `/api/submit-questionnaire`
- ✅ `getChildResults(childId)` → GET `/api/results/{childId}`

---

### 6. **TrainingService** ✨ (جديدة)
- ✅ `getTrainingRoadmap(childId)` → GET `/api/training/roadmap/{childId}`
- ✅ `getGameContent(difficultyId, level)` → GET `/api/game-content/{difficultyId}/{level}`
- ✅ `completeTrainingLevel(childId, trainingType)` → POST `/api/training/complete`
- ✅ `submitGameResult(childId, gameType, rawScore)` → POST `/api/submit-game-result`

---

### 7. **HistoryService** ✨ (جديدة)
- ✅ `getChildHistory(childId)` → GET `/api/child/{childId}/history`
- ✅ `getChildReport(childId)` → GET `/api/child/{childId}/report`

---

### 8. **LearningDifficultyService** ✨ (جديدة)
- ✅ `getAllDifficulties()` → GET `/api/difficulties`
- ✅ `getDifficultyQuestions(difficultyId)` → GET `/api/difficulties/{difficultyId}/questions`

---

### 9. **AdminService** ✨ (جديدة)
- ✅ `getStats()` → GET `/api/admin/stats`
- ✅ `getAllQuestions()` → GET `/api/admin/questions`
- ✅ `createQuestion(data)` → POST `/api/admin/questions`
- ✅ `updateQuestion(id, data)` → PUT `/api/admin/questions/{id}`
- ✅ `deleteQuestion(id)` → DELETE `/api/admin/questions/{id}`

---

## 📋 جدول المطابقة بين Components والـ APIs

| Component/Page | الوظيفة | الخدمة المستخدمة | الـ API |
|---|---|---|---|
| **Login** | تسجيل الدخول | AuthService | POST `/login` |
| **Register** | إنشاء حساب جديد | AuthService | POST `/register` |
| **Profile** | عرض البيانات الشخصية | ProfileService | GET `/user/profile` |
| **Profile** | تحديث البيانات | ProfileService | PUT `/user/profile/update` |
| **Profile** | تغيير كلمة السر | ProfileService | PUT `/user/profile/password` |
| **Profile** | رفع صورة | ProfileService | POST `/user/upload-image` |
| **Profile** | إدارة الأطفال | ChildService | GET/POST `/children` |
| **Dashboard** | الإحصائيات | AdminService | GET `/admin/stats` |
| **Learning Difficulties** | عرض الصعوبات | LearningDifficultyService | GET `/difficulties` |
| **Assessment** | بدء التقييم | AssessmentService | GET `/assessment-content/{id}` |
| **Assessment** | تقديم الإجابات | AssessmentService | POST `/submit-questionnaire` |
| **Assessment** | عرض النتائج | AssessmentService | GET `/results/{childId}` |
| **Training** | عرض مسار التدريب | TrainingService | GET `/training/roadmap/{childId}` |
| **Training** | تحميل محتوى اللعبة | TrainingService | GET `/game-content/{difficulty}/{level}` |
| **Training** | تقديم درجة اللعبة | TrainingService | POST `/submit-game-result` |
| **Training** | إكمال المستوى | TrainingService | POST `/training/complete` |
| **History** | عرض السجل | HistoryService | GET `/child/{childId}/history` |
| **Report** | عرض التقرير الشامل | HistoryService | GET `/child/{childId}/report` |
| **Chatbot** | طرح سؤال | ChatbotService | POST `/chatbot/ask` |
| **Chatbot** | شرح النتائج | ChatbotService | POST `/chatbot/explain-result` |
| **Chatbot** | توصيات تمرينية | ChatbotService | POST `/chatbot/recommend-exercises` |

---

## 🚀 كيفية الاستخدام في Components

### مثال 1: استخدام ChildService
```typescript
import { ChildService } from '@core/services/child.service';

export class ChildComponent {
  constructor(private childService: ChildService) {}

  loadChildren() {
    this.childService.getAllChildren().subscribe({
      next: (response) => {
        console.log('Children:', response.children);
      },
      error: (err) => console.error('Error:', err)
    });
  }

  addChild(name: string, age: number) {
    this.childService.createChild(name, age).subscribe({
      next: (response) => {
        console.log('Child created:', response.child);
      }
    });
  }
}
```

---

### مثال 2: استخدام AssessmentService
```typescript
import { AssessmentService } from '@core/services/assessment.service';

export class AssessmentComponent {
  constructor(private assessmentService: AssessmentService) {}

  loadAssessment(difficultyId: number) {
    this.assessmentService.getAssessmentContent(difficultyId).subscribe({
      next: (response) => {
        this.questions = response.data?.questions;
      }
    });
  }

  submitAnswers(childId: number, answers: any) {
    this.assessmentService.submitQuestionnaire({
      child_id: childId,
      learning_difficulty_id: 1,
      ...answers
    }).subscribe({
      next: (response) => {
        console.log('Questionnaire submitted');
      }
    });
  }
}
```

---

### مثال 3: استخدام TrainingService
```typescript
import { TrainingService } from '@core/services/training.service';

export class TrainingComponent {
  constructor(private trainingService: TrainingService) {}

  loadTraining(childId: number) {
    this.trainingService.getTrainingRoadmap(childId).subscribe({
      next: (response) => {
        this.roadmap = response;
      }
    });
  }

  startGame(difficultyId: number, level: number) {
    this.trainingService.getGameContent(difficultyId, level).subscribe({
      next: (response) => {
        this.gameContent = response;
      }
    });
  }

  submitScore(childId: number, gameType: string, score: number) {
    this.trainingService.submitGameResult(childId, gameType, score).subscribe({
      next: (response) => {
        console.log('Score submitted');
      }
    });
  }
}
```

---

## ⚠️ ملاحظات مهمة

1. **التوكن (Token):**
   - يُحفظ تلقائياً بعد تسجيل الدخول
   - يُضاف تلقائياً في كل طلب عبر **AuthInterceptor**
   - يُحذف عند تسجيل الخروج

2. **معالجة الأخطاء:**
   - إذا كان الكود 401 → يُعاد التوجيه لصفحة تسجيل الدخول
   - كل الخدمات تعيد Observables للتعامل مع الأخطاء

3. **الملفات (Files):**
   - استخدم `FormData` عند رفع الصور
   - تأكد من صحة صيغة الملف

---

## ✅ حالة الربط الحالية

✅ **مكتمل 100%:**
- جميع الـ APIs موجودة في Backend
- جميع الخدمات موجودة في Frontend
- جميع الـ Interceptors والـ Error Handling جاهزة

🚀 **الخطوة التالية:**
- ربط كل Component بالخدمات المناسبة
- تحديث UI لعرض البيانات من الـ API
- اختبار جميع الـ Endpoints

