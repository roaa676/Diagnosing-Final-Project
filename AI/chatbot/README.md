# مساعد بوصلة الذكي — Bowsla Arabic AI Chatbot Module

> وحدة الذكاء الاصطناعي لمنصة **بوصلة** — منصة الكشف المبكر عن صعوبات التعلم لدى الأطفال (أعمار 4–10 سنوات).

---

## ما هو مساعد بوصلة؟

مساعد بوصلة هو **مساعد ذكي عربي** مخصص لأولياء الأمور. يساعدهم على:

1. فهم نتائج اختبارات أطفالهم داخل المنصة بلغة بسيطة وودودة.
2. معرفة ما تعنيه كل مهارة تم قياسها وماذا يستدل من الدرجة.
3. الحصول على توصيات تمارين منزلية مخصصة بناءً على أداء الطفل.
4. الإجابة على أسئلتهم الشائعة عن صعوبات التعلم.
5. تلقي الدعم النفسي والطمأنينة.

### ما لا يفعله المساعد
- ❌ لا يُشخّص طبيًا
- ❌ لا يوصي بأدوية
- ❌ لا يستبدل المتخصص
- ❌ لا يستخدم لغة مخيفة أو طبية معقدة

---

## هيكل المجلد

```
AI/chatbot/
│
├── README.md                          ← هذا الملف
│
├── prompts/                           ← قوالب الـ prompts الجاهزة
│   ├── system_prompt_ar.md            ← هوية المساعد وقواعده الأساسية
│   ├── parent_result_explanation_prompt.md   ← شرح نتيجة الطفل
│   ├── exercise_recommendation_prompt.md     ← توصية التمارين
│   └── faq_prompt.md                  ← الإجابة على الأسئلة الشائعة
│
├── rules/                             ← قواعد السلامة وتفسير الدرجات
│   ├── ar_chatbot_rules.md            ← 21 قاعدة سلامة وسلوك للمساعد
│   ├── skill_rules.json               ← تعريف كل مهارة + تمارين المستويات الثلاثة
│   └── score_rules.json               ← نطاقات الدرجات وتفسيراتها
│
├── services/                          ← PHP service classes (Laravel)
│   ├── ChatbotService.php             ← الـ orchestrator الرئيسي
│   ├── ResultContextBuilder.php       ← تحويل بيانات DB إلى سياق للـ chatbot
│   ├── SafetyFilter.php               ← فلتر السلامة وإزالة التشخيصات المحظورة
│   └── SkillRulesRepository.php       ← تحميل وقراءة ملفات الـ JSON
│
├── controllers/                       ← Laravel Controller جاهز للتكامل
│   └── ChatbotController.php          ← 3 endpoints: ask / explain-result / recommend-exercises
│
├── examples/                          ← أمثلة وبيانات اختبار
│   ├── sample_child_result.json       ← نموذج بيانات الطفل المُرسَل للـ chatbot
│   ├── sample_parent_questions.json   ← أسئلة أولياء الأمور المتوقعة مصنّفة
│   └── sample_chatbot_responses.md    ← نماذج الردود المثالية + قواعد التحقق
│
└── integration/                       ← دليل التكامل مع Laravel
    ├── api_routes_snippet.php         ← مقطع الـ routes الجاهز للصق في api.php
    └── integration_guide.md           ← دليل تفصيلي خطوة بخطوة
```

---

## المهارات المغطاة

### عسر القراءة (Dyslexia)

| skill_id | الاسم العربي | ما تقيسه |
|----------|-------------|----------|
| `dyslexia_visual_discrimination` | التمييز البصري للحروف | دقة الإدراك البصري للفروق بين الحروف |
| `dyslexia_structural_similarity` | تمييز الحروف المتشابهة بنائيًا | التمييز بين ح/خ/ج ، س/ش ، ص/ض |
| `dyslexia_mirroring` | الانعكاس المرآتي للحروف والكلمات | b/d ، بطة/طبة ، ٢/٦ |

### عسر الحساب (Dyscalculia)

| skill_id | الاسم العربي | ما تقيسه |
|----------|-------------|----------|
| `dyscalculia_quantity_comparison` | إدراك الكميات ومقارنتها | الحدس الرقمي الأساسي |
| `dyscalculia_number_sequence` | التسلسل الرقمي | ترتيب الأرقام والرقم الناقص |
| `dyscalculia_number_reversal` | انعكاس الأرقام واتجاهها | 12 vs 21 ، القيمة المكانية |

---

## مقياس الدرجات

| الدرجة | التصنيف | مستوى التدريب |
|--------|----------|--------------|
| 0 – 49 | مؤشر صعوبة مرتفع | المستوى الأول |
| 50 – 69 | مؤشر صعوبة متوسط | المستوى الثاني |
| 70 – 100 | أداء جيد | المستوى الثالث |

> جميع الدرجات **مؤشرات** وليست تشخيصات نهائية.

---

## الـ Endpoints

| الطريقة | المسار | الوصف |
|---------|--------|-------|
| POST | `/api/chatbot/ask` | سؤال حر من ولي الأمر |
| POST | `/api/chatbot/explain-result` | شرح نتيجة الطفل كاملة |
| POST | `/api/chatbot/recommend-exercises` | توصية تمارين مخصصة |

جميع الـ endpoints تتطلب `Authorization: Bearer TOKEN` (Sanctum).

---

## تدفق العمل

```
[ولي الأمر يسأل]
        ↓
[ChatbotController]
        ↓
[ResultContextBuilder]   ← يجلب GameResult + Questionnaire من DB
        ↓                   ويحوّلها لسياق JSON منظّم
[ChatbotService]
        ↓
  هل OPENAI_API_KEY موجود؟
  ┌─── نعم ────────────────────────────────────────────────────┐
  │                                                             │
  │  يبني الـ prompt = system_prompt + child_context + question │
  │         ↓                                                   │
  │  يرسل لـ OpenAI API (gpt-4o)                               │
  │         ↓                                                   │
  │  [SafetyFilter] ← يفحص الرد ويُزيل أي تشخيصات محظورة     │
  │         ↓                                                   │
  │  يُضيف disclaimer إذا كان الرد يشرح نتيجة                 │
  └─────────────────────────────────────────────────────────────┘
  ┌─── لا ─────────────────────────────────────────────────────┐
  │  [SafetyFilter::buildFallbackResponse()]                    │
  │  يبني رد قاعدي عربي من skill_rules.json + score_rules.json │
  └─────────────────────────────────────────────────────────────┘
        ↓
[JSON Response للـ Frontend]
```

---

## إعداد متغيرات البيئة

في `backend/.env`:

```env
OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxxxxxxxxxxxxx
OPENAI_MODEL=gpt-4o
OPENAI_MAX_TOKENS=1000
```

في `backend/config/services.php`:

```php
'openai' => [
    'key'        => env('OPENAI_API_KEY', ''),
    'model'      => env('OPENAI_MODEL', 'gpt-4o'),
    'max_tokens' => env('OPENAI_MAX_TOKENS', 1000),
],
```

---

## للتكامل السريع

راجع [integration/integration_guide.md](integration/integration_guide.md) للخطوات التفصيلية (8 خطوات).

الخلاصة السريعة:
1. انسخ `services/` → `backend/app/Services/Chatbot/`
2. انسخ `controllers/ChatbotController.php` → `backend/app/Http/Controllers/`
3. أضف الـ routes من `integration/api_routes_snippet.php` إلى `backend/routes/api.php`
4. أضف `OPENAI_API_KEY` إلى `.env`
5. أضف OpenAI config إلى `config/services.php`

---

## القواعد الذهبية للمساعد

1. **يُجيب بالعربية دائمًا.**
2. **لا يُشخّص طبيًا أبدًا.**
3. **كل رد يحتوي على خطوة عملية.**
4. **يُرفق disclaimer عند شرح النتائج.**
5. **يُوجّه للمتخصص عند الحاجة، بأسلوب داعم لا مخيف.**

---

## إضافة مهارات جديدة

لإضافة مهارة جديدة للنظام:

1. أضف الكائن الكامل في `rules/skill_rules.json` بنفس هيكل المهارات الموجودة.
2. أضف الـ mapping في `services/ResultContextBuilder.php` → `$gameTypeToSkillId`.
3. أضف الأمثلة في `ResultContextBuilder::getMistakeExamples()`.
4. لا حاجة لتعديل أي ملف آخر — النظام يقرأ القواعد ديناميكيًا.

---

*بوصلة — لأن كل طفل يستحق أن يُكتشف مبكرًا.*
