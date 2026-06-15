# 📊 API Response Structure للـ 3 Screens

## 🎯 الشاشات الثلاثة وشكل البيانات المتوقعة

---

## 1️⃣ **شاشة استبيان ولي الأمر** 
(Parent Initial Questionnaire Screen)

### 📥 **Request**
```
POST /api/submit-questionnaire
Headers: Authorization: Bearer {token}

Body:
{
  "child_id": 1,
  "learning_difficulty_id": 1,
  "q1_reading_aloud": 1,
  "q2_confusing_letters": 2,
  "q3_forgetting_instructions": 1,
  "q4_avoiding_reading": 2
}

Note:
- كل سؤال يأخذ قيمة 0-2 (0=لا، 1=أحياناً، 2=نعم)
- يتم تجميع الـ scores: 0-8 نقاط
```

### 📤 **Response**
```json
{
  "status": "success",
  "message": "تم حفظ التقييم بنجاح",
  "risk_level": "high",
  "recommendation": {
    "training_level": 1,
    "message": "يحتاج الطفل إلى تدريب مكثف في المستوى الأول",
    "guidance": "ننصح بممارسة تمارين يومية (10-15 دقيقة) لمدة 4-6 أسابيع"
  },
  "data": {
    "id": 15,
    "child_id": 1,
    "q1_reading_aloud": 1,
    "q2_confusing_letters": 2,
    "q3_forgetting_instructions": 1,
    "q4_avoiding_reading": 2,
    "total_risk_score": 6,
    "risk_level": "high",
    "created_at": "2024-06-11T14:30:00Z"
  }
}
```

### 📝 **الأسئلة الثابتة** (4 أسئلة فقط)
```
1. هل الطفل يقرأ بصوت عالي بصعوبة؟
2. هل يخلط بين الحروف المتشابهة (ب، د)؟
3. هل ينسى التعليمات بسرعة؟
4. هل يتجنب القراءة؟

كل سؤال ← 3 خيارات:
- 0 = لا
- 1 = أحياناً
- 2 = نعم
```

### 🎨 **UI Mockup**
```
┌─────────────────────────────────┐
│  استبيان التقييم المبدئي       │
│  للطفل: أحمد (8 سنوات)           │
├─────────────────────────────────┤
│                                 │
│ 1️⃣ هل يقرأ بصعوبة؟             │
│    ⭕ لا   ⭕ أحياناً   ⭕ نعم  │
│                                 │
│ 2️⃣ يخلط بين الحروف؟            │
│    ⭕ لا   ⭕ أحياناً   ⭕ نعم  │
│                                 │
│ 3️⃣ ينسى التعليمات؟             │
│    ⭕ لا   ⭕ أحياناً   ⭕ نعم  │
│                                 │
│ 4️⃣ يتجنب القراءة؟              │
│    ⭕ لا   ⭕ أحياناً   ⭕ نعم  │
│                                 │
│              [تقديم]             │
├─────────────────────────────────┤
│ * هذا تقييم مبدئي وليس تشخيص    │
└─────────────────────────────────┘
```

---

## 2️⃣ **شاشة التقييم المبدئي للطفل**
(Initial Child Assessment Screen)

### 📥 **Request**
```
GET /api/assessment-content/{difficulty_id}
GET /api/assessment-content/1  ← for عسر القراءة
GET /api/assessment-content/2  ← for عسر الحساب
...

Headers: Authorization: Bearer {token}
```

### 📤 **Response**
```json
{
  "status": "success",
  "data": {
    "difficulty_id": 1,
    "difficulty_name": "عسر القراءة (ديسلكسيا)",
    "description": "صعوبة في قراءة وتهجئة الكلمات",
    "icon": "📖",
    "questions": [
      {
        "id": 1,
        "question": "اختر الكلمة الصحيحة: هذا...",
        "type": "multiple_choice",
        "options": [
          {"id": 1, "text": "بطة"},
          {"id": 2, "text": "طبة"},
          {"id": 3, "text": "دطة"}
        ],
        "correct_answer": 1,
        "time_limit": 30
      },
      {
        "id": 2,
        "question": "هل حرف b و d متشابهان؟",
        "type": "true_false",
        "options": [
          {"id": 1, "text": "نعم"},
          {"id": 2, "text": "لا"}
        ],
        "correct_answer": 1,
        "time_limit": 15
      },
      {
        "id": 3,
        "question": "رتب الحروف لتكوين كلمة صحيحة",
        "type": "ordering",
        "options": [
          {"id": 1, "text": "د"},
          {"id": 2, "text": "ر"},
          {"id": 3, "text": "س"}
        ],
        "correct_answer": [1, 2, 3],
        "time_limit": 45
      }
    ]
  }
}
```

### 📝 **أنواع الأسئلة**
```
1. Multiple Choice (اختيار من متعدد)
   - 3-4 خيارات
   - خيار واحد صحيح

2. True/False (صح/خطأ)
   - خيارين فقط
   - إجابة واحدة صحيحة

3. Ordering/Sequencing (الترتيب)
   - رتب العناصر بالترتيب الصحيح
   - مهم جداً لتقييم عسر الحساب وعسر الكتابة

4. Matching (المطابقة)
   - ربط العناصر ببعضها
```

### 🎨 **UI Mockup**
```
┌──────────────────────────────────────┐
│     تقييم عسر القراءة - السؤال 1/5  │
├──────────────────────────────────────┤
│                                      │
│  ⏱️ 0:30 ثانية المتبقية            │
│                                      │
│  📖 اختر الكلمة الصحيحة:             │
│     هذا حيوان صغير يعيش في الماء    │
│                                      │
│   🔘 بطة    🔘 طبة    🔘 دطة       │
│                                      │
│  [التالي]  [تخطي]                   │
├──────────────────────────────────────┤
│  الإجابة الصحيحة: بطة                │
│  شرح: تذكر b يذهب لليمين...         │
└──────────────────────────────────────┘
```

---

## 3️⃣ **شاشة أسئلة التدريب**
(Training Questions/Game Screen)

### 📥 **Request**
```
GET /api/game-content/{difficulty_id}/{level}
GET /api/game-content/1/1  ← level 1 for عسر القراءة
GET /api/game-content/1/2  ← level 2 for عسر القراءة
...

Headers: Authorization: Bearer {token}
```

### 📤 **Response**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "type": "visual_discrimination",
    "title": "لعبة التمييز البصري",
    "difficulty": 1,
    "description": "اختر الكلمة المختلفة",
    "total_questions": 10,
    "time_per_question": 20,
    "total_time": 200,
    "instructions": "اختر الكلمة التي تختلف عن الباقي",
    "questions": [
      {
        "id": 1,
        "question": "أي كلمة مختلفة؟",
        "type": "multiple_choice",
        "options": [
          {"id": 1, "text": "بطة"},
          {"id": 2, "text": "بطة"},
          {"id": 3, "text": "طبة"},
          {"id": 4, "text": "بطة"}
        ],
        "correct_answer": 3,
        "explanation": "طبة مختلفة - الحروف مقلوبة!",
        "points": 10
      },
      {
        "id": 2,
        "question": "أين الحرف الصحيح؟",
        "type": "matching",
        "pairs": [
          {
            "left": {"id": 1, "text": "b"},
            "right": [
              {"id": 1, "text": "b"},
              {"id": 2, "text": "d"},
              {"id": 3, "text": "p"}
            ],
            "correct": 1
          }
        ],
        "points": 10
      }
    ]
  }
}
```

### 📋 **أنواع ألعاب التدريب**
```
Level 1 (مكثف - High Difficulty):
├─ Visual Discrimination (تمييز بصري)
├─ Letter Matching (مطابقة الحروف)
├─ Simple Sequencing (ترتيب بسيط)
└─ Flash Cards (بطاقات وميضة)

Level 2 (تعزيزي - Moderate Difficulty):
├─ Word Building (بناء كلمات)
├─ Sentence Reading (قراءة جمل)
├─ Rhythm & Rhyme (الإيقاع والقافية)
└─ Story Comprehension (فهم القصة)

Level 3 (صيانة - Good Performance):
├─ Reading Speed (سرعة القراءة)
├─ Comprehension Tests (اختبارات الفهم)
├─ Writing Practice (تمارين الكتابة)
└─ Advanced Games (ألعاب متقدمة)
```

### 📤 **Post Result**
```
POST /api/submit-game-result
Headers: Authorization: Bearer {token}

Body:
{
  "child_id": 1,
  "game_type": "visual_discrimination",
  "raw_score": 85,
  "answers": [
    {"question_id": 1, "selected": 3, "correct": 3, "points": 10},
    {"question_id": 2, "selected": 1, "correct": 1, "points": 10}
  ],
  "time_taken": 150,
  "completed_at": "2024-06-11T15:30:00Z"
}

Response:
{
  "status": "success",
  "message": "تم حفظ النتيجة بنجاح",
  "result": {
    "raw_score": 85,
    "normalized_score": 92,
    "performance": "ممتاز",
    "next_level": 2,
    "feedback": "تمام يا بطل! تقدمت جداً 🎉"
  }
}
```

### 🎨 **UI Mockup**
```
┌──────────────────────────────────────┐
│  لعبة التمييز البصري - 1/10          │
│                                      │
│  ⭐⭐⭐⭐⭐ 50 نقطة     ⏱️ 0:20   │
├──────────────────────────────────────┤
│                                      │
│  أي كلمة مختلفة؟                     │
│                                      │
│   [ بطة ]  [ بطة ]  [ طبة ]  [ بطة ] │
│                                      │
│  💡 تذكر: b و d معكوسان             │
│                                      │
├──────────────────────────────────────┤
│  ✅ الإجابات الصحيحة: 8/10          │
│  ⏱️ الوقت المتبقي: 02:30            │
└──────────────────────────────────────┘
```

---

## 📊 ملخص البيانات

| الشاشة | عدد الأسئلة | نوع البيانات | الـ API |
|--------|-----------|------------|--------|
| **الاستبيان** | 4 ثابتة | Multiple Choice (0-2) | POST `/submit-questionnaire` |
| **التقييم المبدئي** | 5-10 | Mixed Types | GET `/assessment-content/{id}` |
| **التدريب/اللعبة** | 10-20 | Mixed Types | GET `/game-content/{diff}/{level}` |

---

## 🔄 سير العملية

```
1. الاستبيان (Parent Questionnaire)
   ↓
   تحديد المستوى: 0-8 نقاط
   ├─ 0-3: منخفض (Level 1)
   ├─ 4-6: متوسط (Level 2)
   └─ 7-8: عالي (Level 3)
   ↓
2. التقييم المبدئي (Initial Assessment)
   ↓
   قياس الأداء الفعلي للطفل
   ├─ صح: +نقاط
   └─ خطأ: -نقاط
   ↓
3. التدريب/اللعبة (Training Game)
   ↓
   تمرينات يومية لتحسين المهارات
```

---

## 💡 نقاط مهمة للـ UI

✅ **إضافة:**
- عداد الوقت لكل سؤال
- تقدم العملية (X من Y)
- نقاط المكافآت
- شرح بعد كل إجابة
- رسائل تحفيزية

✅ **ألوان المستويات:**
- Level 1: 🔴 أحمر (مكثف)
- Level 2: 🟡 برتقالي (تعزيزي)
- Level 3: 🟢 أخضر (صيانة)

✅ **التسلسل:**
1. الاستبيان أولاً (5 دقائق)
2. ثم التقييم (10 دقائق)
3. ثم بدء التدريب (يومي)

---

