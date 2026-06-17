# 📡 Complete API Reference - مرجع شامل لجميع الـ APIs

## 🔐 Authentication APIs

### 1. **Login** - تسجيل الدخول
```
POST /api/login

Request Body:
{
  "email": "ahmed@example.com",
  "password": "password123"
}

Response (200 OK):
{
  "token": "3|HnrhYx9I66HvrLllWSYhwaYJfe3ePTsnhWy5mF5A7823d013",
  "message": "تم تسجيل الدخول بنجاح",
  "user": {
    "id": 1,
    "name": "Ahmed",
    "email": "ahmed@example.com"
  }
}

Service: AuthService.login(email, password)
```

---

### 2. **Register** - إنشاء حساب جديد
```
POST /api/register

Request Body:
{
  "name": "Ahmed",
  "email": "ahmed@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "children": [
    {
      "name": "momen",
      "age": 8
    }
  ]
}

Response (201 Created):
{
  "token": "3|HnrhYx9I66HvrLllWSYhwaYJfe3ePTsnhWy5mF5A7823d013",
  "message": "تم التسجيل بنجاح",
  "user": {
    "id": 1,
    "name": "Ahmed",
    "email": "ahmed@example.com"
  }
}

Service: AuthService.register(name, email, password, confirmPassword, children)
```

---

## 👤 User Profile APIs

### 3. **Get Profile** - الحصول على الملف الشخصي
```
GET /api/user/profile
Headers: Authorization: Bearer {token}

Response (200 OK):
{
  "status": "success",
  "user": {
    "id": 1,
    "name": "Ahmed",
    "email": "ahmed@example.com",
    "phone": "01012345678",
    "image_url": "storage/profiles/user_1.jpg",
    "created_at": "2024-01-15T10:30:00Z"
  }
}

Service: ProfileService.getProfile()
```

---

### 4. **Update Profile** - تحديث الملف الشخصي
```
PUT /api/user/profile/update
Headers: Authorization: Bearer {token}

Request Body:
{
  "name": "Ahmed Mohamed",
  "phone": "01098765432"
}

Response (200 OK):
{
  "status": "success",
  "message": "تم تحديث البيانات بنجاح",
  "user": { ... }
}

Service: ProfileService.updateProfile(userData)
```

---

### 5. **Change Password** - تغيير كلمة المرور
```
PUT /api/user/profile/password
Headers: Authorization: Bearer {token}

Request Body:
{
  "current_password": "password123",
  "new_password": "newpassword456",
  "new_password_confirmation": "newpassword456"
}

Response (200 OK):
{
  "status": "success",
  "message": "تم تغيير كلمة المرور بنجاح"
}

Service: ProfileService.changePassword(passwordData)
```

---

### 6. **Upload Profile Image** - رفع صورة الملف الشخصي
```
POST /api/user/upload-image
Headers: Authorization: Bearer {token}
Content-Type: multipart/form-data

Request Body (FormData):
{
  "image": File
}

Response (200 OK):
{
  "status": "success",
  "message": "تم رفع الصورة بنجاح",
  "image_url": "storage/profiles/user_1_2024.jpg"
}

Service: ProfileService.uploadProfileImage(file)
```

---

## 👶 Children Management APIs

### 7. **Get All Children** - الحصول على جميع الأطفال
```
GET /api/children
Headers: Authorization: Bearer {token}

Response (200 OK):
{
  "status": "success",
  "children": [
    {
      "id": 1,
      "name": "أحمد",
      "age": 8,
      "created_at": "2024-01-15T10:30:00Z"
    },
    {
      "id": 2,
      "name": "فاطمة",
      "age": 6,
      "created_at": "2024-01-15T10:35:00Z"
    }
  ]
}

Service: ChildService.getAllChildren()
```

---

### 8. **Create Child** - إضافة طفل جديد
```
POST /api/children
Headers: Authorization: Bearer {token}

Request Body:
{
  "name": "عمر",
  "age": 7
}

Response (201 Created):
{
  "status": "success",
  "message": "تم إضافة الطفل بنجاح",
  "child": {
    "id": 3,
    "name": "عمر",
    "age": 7,
    "created_at": "2024-01-20T15:00:00Z"
  }
}

Service: ChildService.createChild(name, age)
```

---

### 9. **Upload Child Image** - رفع صورة الطفل
```
POST /api/child/{child_id}/upload-image
Headers: Authorization: Bearer {token}
Content-Type: multipart/form-data

Request Body (FormData):
{
  "image": File
}

Response (200 OK):
{
  "status": "success",
  "message": "تم رفع الصورة بنجاح",
  "image_url": "storage/children/child_3.jpg"
}

Service: ChildService.uploadChildImage(childId, file)
```

---

## 📚 Learning Difficulties APIs

### 10. **Get All Difficulties** - الحصول على جميع الصعوبات
```
GET /api/difficulties
(No auth required)

Response (200 OK):
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "عسر القراءة (ديسلكسيا)",
      "description": "صعوبة في قراءة وتهجئة الكلمات",
      "icon": "book"
    },
    {
      "id": 2,
      "name": "عسر الحساب (ديسكالكوليا)",
      "description": "صعوبة في العمليات الحسابية",
      "icon": "calculator"
    }
  ]
}

Service: LearningDifficultyService.getAllDifficulties()
```

---

### 11. **Get Difficulty Questions** - الحصول على أسئلة الصعوبة
```
GET /api/difficulties/{difficulty_id}/questions
Headers: Authorization: Bearer {token}

Example:/api/assessment-content/{difficultyId}

Response (200 OK):
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "text": "هل الطفل يخلط بين الحروف المتشابهة؟",
      "options": ["نعم", "أحياناً", "لا"],
      "answer": "نعم"
    }
  ]
}

Service: LearningDifficultyService.getDifficultyQuestions(difficultyId)
```

---

## 📋 Assessment APIs

### 12. **Get Assessment Content** - الحصول على محتوى التقييم
```
GET /api/assessment-content/{difficulty_id}
Headers: Authorization: Bearer {token}

Example: /api/assessment-content/1

Response (200 OK):
{
  "status": "success",
  "data": {
    "difficulty_id": 1,
    "difficulty_name": "عسر القراءة",
    "description": "اختبار شامل لعسر القراءة",
    "questions": [
      {
        "id": 1,
        "question": "هل يواجه الطفل صعوبة في قراءة الكلمات؟",
        "type": "multiple_choice",
        "options": ["نعم", "أحياناً", "لا"]
      }
    ]
  }
}

Service: AssessmentService.getAssessmentContent(difficultyId)
```

---

### 13. **Submit Questionnaire** - تقديم الاستبيان
```
POST /api/submit-questionnaire
Headers: Authorization: Bearer {token}

Request Body:
{
  "child_id": 1,
  "learning_difficulty_id": 1,
  "q1_reading_aloud": 1,
  "q2_confusing_letters": 2,
  "q3_forgetting_instructions": 1,
  "q4_avoiding_reading": 2
}

Response (200 OK):
{
  "status": "success",
  "message": "تم تقديم الاستبيان بنجاح",
  "result": {
    "child_id": 1,
    "difficulty_id": 1,
    "score": 85,
    "status": "يحتاج تدريب"
  }
}

Service: AssessmentService.submitQuestionnaire(data)
```

---

### 14. **Get Assessment Results** - الحصول على النتائج
```
GET /api/results/{child_id}
Headers: Authorization: Bearer {token}

Example: /api/results/1

Response (200 OK):
{
  "status": "success",
  "data": {
    "child_id": 1,
    "total_assessments": 3,
    "results": [
      {
        "id": 1,
        "difficulty_name": "عسر القراءة",
        "score": 85,
        "date": "2024-01-20"
      }
    ]
  }
}

Service: AssessmentService.getChildResults(childId)
```

---

## 🎮 Training & Games APIs

### 15. **Get Training Roadmap** - الحصول على مسار التدريب
```
GET /api/training/roadmap/{child_id}
Headers: Authorization: Bearer {token}

Example: /api/training/roadmap/1

Response (200 OK):
{
  "status": "success",
  "data": {
    "child_id": 1,
    "progress": 45,
    "levels": [
      {
        "id": 1,
        "name": "المستوى الأول",
        "difficulty": 1,
        "description": "تمارين بسيطة",
        "completed": true
      },
      {
        "id": 2,
        "name": "المستوى الثاني",
        "difficulty": 2,
        "description": "تمارين متوسطة",
        "completed": false
      }
    ]
  }
}

Service: TrainingService.getTrainingRoadmap(childId)
```

---

### 16. **Get Game Content** - الحصول على محتوى اللعبة
```
GET /api/game-content/{difficulty_id}/{level}
Headers: Authorization: Bearer {token}

Example: /api/game-content/1/1

Response (200 OK):
{
  "status": "success",
  "data": {
    "id": 1,
    "type": "visual_discrimination",
    "title": "ألعاب التمييز البصري",
    "difficulty_level": 1,
    "content": {
      "instructions": "اختر الكلمة الصحيحة",
      "items": [
        {
          "pair": ["بطة", "طبة"],
          "correct": 0
        }
      ]
    }
  }
}

Service: TrainingService.getGameContent(difficultyId, level)
```

---

### 17. **Submit Game Result** - تقديم نتيجة اللعبة
```
POST /api/submit-game-result
Headers: Authorization: Bearer {token}

Request Body:
{
  "child_id": 1,
  "game_type": "visual_discrimination",
  "raw_score": 85
}

Response (200 OK):
{
  "status": "success",
  "message": "تم حفظ النتيجة بنجاح",
  "result": {
    "score": 85,
    "normalized_score": 92,
    "performance": "ممتاز"
  }
}

Service: TrainingService.submitGameResult(childId, gameType, rawScore)
```

---

### 18. **Complete Training Level** - إكمال مستوى التدريب
```
POST /api/training/complete
Headers: Authorization: Bearer {token}

Request Body:
{
  "child_id": 1,
  "training_type": "1"
}

Response (200 OK):
{
  "status": "success",
  "message": "تم إكمال المستوى بنجاح",
  "next_level": 2
}

Service: TrainingService.completeTrainingLevel(childId, trainingType)
```

---

## 📊 History & Reports APIs

### 19. **Get Child History** - الحصول على سجل الطفل
```
GET /api/child/{child_id}/history
Headers: Authorization: Bearer {token}

Example: /api/child/1/history

Response (200 OK):
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "activity_type": "assessment",
      "description": "تقييم عسر القراءة",
      "timestamp": "2024-01-20T15:30:00Z",
      "result": {
        "score": 85,
        "difficulty": "عسر القراءة"
      }
    },
    {
      "id": 2,
      "activity_type": "game",
      "description": "لعبة التمييز البصري",
      "timestamp": "2024-01-20T16:00:00Z",
      "result": {
        "score": 92
      }
    }
  ]
}

Service: HistoryService.getChildHistory(childId)
```

---

### 20. **Get Comprehensive Report** - الحصول على التقرير الشامل
```
GET /api/child/{child_id}/report
Headers: Authorization: Bearer {token}

Example: /api/child/1/report

Response (200 OK):
{
  "status": "success",
  "data": {
    "child_id": 1,
    "child_name": "أحمد",
    "age": 8,
    "overall_progress": 62,
    "assessments": [
      {
        "difficulty": "عسر القراءة",
        "score": 85,
        "status": "يحتاج تدريب",
        "recommendations": ["تمارين قراءة يومية", "ألعاب تمييز كلمات"]
      }
    ],
    "training_progress": {
      "total_levels": 5,
      "completed_levels": 3,
      "current_level": 4
    },
    "game_statistics": {
      "total_games": 20,
      "average_score": 78,
      "best_score": 95
    }
  }
}

Service: HistoryService.getChildReport(childId)
```

---

## 🤖 Chatbot APIs

### 21. **Ask Chatbot** - طرح سؤال على المساعد
```
POST /api/chatbot/ask
Headers: Authorization: Bearer {token}

Request Body:
{
  "message": "يعني إيه عسر القراءة؟",
  "child_id": 1  // اختياري
}

Response (200 OK):
{
  "status": "success",
  "answer": "عسر القراءة (الديسلكسيا) هو صعوبة تعلم تؤثر على..."
}

Service: ChatbotService.ask(message, childId)
```

---

### 22. **Explain Result** - شرح النتائج
```
POST /api/chatbot/explain-result
Headers: Authorization: Bearer {token}

Request Body:
{
  "child_id": 1,
  "result_id": 5  // اختياري
}

Response (200 OK):
{
  "status": "success",
  "answer": "نتيجة التقييم تشير إلى أن الطفل..."
}

Service: ChatbotService.explainResult(childId, resultId)
```

---

### 23. **Recommend Exercises** - توصيات التمارين
```
POST /api/chatbot/recommend-exercises
Headers: Authorization: Bearer {token}

Request Body:
{
  "child_id": 1
}

Response (200 OK):
{
  "status": "success",
  "answer": "بناءً على نتائج الطفل، أنصح بـ...",
  "recommended_exercises": [
    {
      "name": "تمارين القراءة السريعة",
      "description": "قراءة كلمات مختلفة بسرعة",
      "duration": "15 دقيقة"
    }
  ]
}

Service: ChatbotService.recommendExercises(childId)
```

---

## ⚙️ Admin APIs

### 24. **Get Admin Stats** - الحصول على الإحصائيات
```
GET /api/admin/stats
Headers: Authorization: Bearer {admin_token}

Response (200 OK):
{
  "status": "success",
  "data": {
    "total_users": 150,
    "total_children": 320,
    "total_assessments": 1200,
    "avg_score": 78.5
  }
}

Service: AdminService.getStats()
```

---

### 25. **Get All Questions** - الحصول على جميع الأسئلة
```
GET /api/admin/questions
Headers: Authorization: Bearer {admin_token}

Response (200 OK):
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "text": "هل الطفل يخلط بين الحروف؟",
      "difficulty_id": 1,
      "category": "reading"
    }
  ]
}

Service: AdminService.getAllQuestions()
```

---

### 26. **Create Question** - إضافة سؤال جديد
```
POST /api/admin/questions
Headers: Authorization: Bearer {admin_token}

Request Body:
{
  "text": "هل يواجه الطفل مشاكل في الكتابة؟",
  "difficulty_id": 1,
  "category": "writing"
}

Response (201 Created):
{
  "status": "success",
  "data": { ... }
}

Service: AdminService.createQuestion(questionData)
```

---

### 27. **Update Question** - تحديث سؤال
```
PUT /api/admin/questions/{question_id}
Headers: Authorization: Bearer {admin_token}

Request Body:
{
  "text": "هل يواجه الطفل مشاكل في الكتابة أو التهجئة؟",
  "category": "writing"
}

Response (200 OK):
{
  "status": "success",
  "data": { ... }
}

Service: AdminService.updateQuestion(questionId, questionData)
```

---

### 28. **Delete Question** - حذف سؤال
```
DELETE /api/admin/questions/{question_id}
Headers: Authorization: Bearer {admin_token}

Response (200 OK):
{
  "status": "success",
  "message": "تم حذف السؤال بنجاح"
}

Service: AdminService.deleteQuestion(questionId)
```

---

## 🎯 ملخص الـ Status Codes

| Code | المعنى | الحالة |
|------|--------|--------|
| **200** | OK | العملية نجحت |
| **201** | Created | تم إنشاء مورد جديد |
| **400** | Bad Request | بيانات غير صحيحة |
| **401** | Unauthorized | توكن غير صحيح أو منتهي الصلاحية |
| **403** | Forbidden | لا توجد صلاحيات |
| **404** | Not Found | المورد غير موجود |
| **500** | Server Error | خطأ في الخادم |

---

## 🔐 ملاحظات الأمان

✅ **جميع الـ APIs محمية بـ Token** (ما عدا Login و Register و Get Difficulties)
✅ **التوكن يُضاف تلقائياً** عبر AuthInterceptor
✅ **معالجة تلقائية للـ 401** - التوجيه لصفحة Login

