# دليل تكامل مساعد بوصلة مع Backend Laravel

> هذا الدليل يشرح كيفية نقل ملفات الـ AI/chatbot إلى مشروع Laravel الخاص ببوصلة وتشغيلها.

---

## الخطوة 1 — نسخ ملفات الخدمات

قم بنسخ ملفات الـ PHP من مجلد `AI/chatbot/` إلى مشروع Laravel:

```bash
# من مجلد Diagnosing-Final-Project/

# 1. نسخ الخدمات
mkdir -p backend/app/Services/Chatbot
cp AI/chatbot/services/ChatbotService.php         backend/app/Services/Chatbot/
cp AI/chatbot/services/ResultContextBuilder.php   backend/app/Services/Chatbot/
cp AI/chatbot/services/SafetyFilter.php           backend/app/Services/Chatbot/
cp AI/chatbot/services/SkillRulesRepository.php   backend/app/Services/Chatbot/

# 2. نسخ الكنترولر
cp AI/chatbot/controllers/ChatbotController.php   backend/app/Http/Controllers/
```

---

## الخطوة 2 — تحديث Namespace الملفات

بعد النسخ، تأكد أن namespace كل ملف صحيح:

| الملف | الـ Namespace الصحيح |
|-------|---------------------|
| ChatbotService.php | `App\Services\Chatbot` |
| ResultContextBuilder.php | `App\Services\Chatbot` |
| SafetyFilter.php | `App\Services\Chatbot` |
| SkillRulesRepository.php | `App\Services\Chatbot` |
| ChatbotController.php | `App\Http\Controllers` |

---

## الخطوة 3 — إضافة مسارات API

افتح `backend/routes/api.php` وأضف:

**أولاً:** في أعلى الملف (مع باقي الـ imports):
```php
use App\Http\Controllers\ChatbotController;
```

**ثانياً:** داخل `Route::middleware('auth:sanctum')->group(...)` أضف:
```php
// ════ مسارات المساعد الذكي ════
Route::prefix('chatbot')->group(function () {
    Route::post('/ask',                  [ChatbotController::class, 'ask']);
    Route::post('/explain-result',       [ChatbotController::class, 'explainResult']);
    Route::post('/recommend-exercises',  [ChatbotController::class, 'recommendExercises']);
});
```

---

## الخطوة 4 — إعداد ملف .env

أضف هذه القيم إلى `backend/.env`:

```env
# ───── Chatbot / OpenAI ─────
OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxxxxxxxxxxx
OPENAI_MODEL=gpt-4o
OPENAI_MAX_TOKENS=1000
```

> **ملاحظة:** إذا لم يكن لديك مفتاح OpenAI حتى الآن، ستعمل الخدمة في **وضع الاحتياط** وتُعيد ردودًا قاعدية باللغة العربية.

---

## الخطوة 5 — إضافة OpenAI إلى config/services.php

افتح `backend/config/services.php` وأضف:

```php
'openai' => [
    'key'        => env('OPENAI_API_KEY', ''),
    'model'      => env('OPENAI_MODEL', 'gpt-4o'),
    'max_tokens' => env('OPENAI_MAX_TOKENS', 1000),
],
```

---

## الخطوة 6 — ضبط مسار ملفات AI

ملف `SkillRulesRepository.php` يبحث عن ملفات الـ JSON في:
```
base_path('../AI/chatbot/rules/skill_rules.json')
base_path('../AI/chatbot/rules/score_rules.json')
```

`base_path()` في Laravel يشير إلى `backend/`، لذا `../AI/` يشير إلى `Diagnosing-Final-Project/AI/`.

إذا كان هيكل مشروعك مختلفًا، عدّل المسارات في:
- `SkillRulesRepository::__construct()`
- `ChatbotService::__construct()` (مسار system_prompt_ar.md)
- `ChatbotService::buildExplainResultMessage()` (مسارات باقي الـ prompts)

---

## الخطوة 7 — تحديث AppServiceProvider (اختياري)

إذا أردت Dependency Injection تلقائيًا، أضف إلى `backend/app/Providers/AppServiceProvider.php`:

```php
use App\Services\Chatbot\ChatbotService;
use App\Services\Chatbot\ResultContextBuilder;
use App\Services\Chatbot\SafetyFilter;
use App\Services\Chatbot\SkillRulesRepository;

public function register(): void
{
    $this->app->singleton(SkillRulesRepository::class);
    $this->app->singleton(SafetyFilter::class);
    $this->app->singleton(ResultContextBuilder::class);
    $this->app->singleton(ChatbotService::class);
}
```

> **ملاحظة:** Laravel يدعم automatic dependency resolution بدون هذا الـ register، لكن الـ singleton يُحسّن الأداء بتجنب إعادة تحميل ملفات الـ JSON في كل request.

---

## الخطوة 8 — اختبار الـ Endpoints

### اختبار `/api/chatbot/ask`
```bash
curl -X POST http://localhost:8000/api/chatbot/ask \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "child_id": 1,
    "message": "يعني إيه انعكاس مرآتي؟"
  }'
```

### اختبار `/api/chatbot/explain-result`
```bash
curl -X POST http://localhost:8000/api/chatbot/explain-result \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "child_id": 1
  }'
```

### اختبار `/api/chatbot/recommend-exercises`
```bash
curl -X POST http://localhost:8000/api/chatbot/recommend-exercises \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "child_id": 1,
    "result_id": 15
  }'
```

---

## بنية الـ Request والـ Response

### POST /api/chatbot/ask

**Request:**
```json
{
  "child_id": 1,
  "message": "ابني جاب نتيجة قليلة، ده معناه إيه؟"
}
```

**Response:**
```json
{
  "status": "success",
  "answer": "..."
}
```

---

### POST /api/chatbot/explain-result

**Request:**
```json
{
  "child_id": 1,
  "result_id": 15
}
```

**Response:**
```json
{
  "status": "success",
  "answer": "بناءً على أداء الطفل داخل منصة بوصلة..."
}
```

---

### POST /api/chatbot/recommend-exercises

**Request:**
```json
{
  "child_id": 1,
  "result_id": 15
}
```

**Response:**
```json
{
  "status": "success",
  "answer": "...",
  "recommended_exercises": [
    {
      "skill_id": "dyslexia_mirroring",
      "arabic_name": "الانعكاس المرآتي للحروف والكلمات",
      "training_level": 1,
      "exercises": [
        { "name": "مطابقة الحرف مع اتجاهه الصحيح", "description": "..." }
      ]
    }
  ]
}
```

---

## وضع الاحتياط (Fallback Mode)

إذا لم يكن `OPENAI_API_KEY` مضبوطًا أو فشل الاتصال بـ OpenAI:

- يرسل المساعد ردًا قاعديًا باللغة العربية مبنيًا على `skill_rules.json` و `score_rules.json`.
- لا يتوقف النظام أو يُرجع خطأ للمستخدم.
- يُسجَّل تحذير في `storage/logs/laravel.log`.

---

## أمان وخصوصية

- جميع الـ endpoints تتطلب `auth:sanctum` — لا أحد يصل لبيانات طفل آخر.
- `ResultContextBuilder` يتحقق دائمًا من ملكية الطفل (`user_id`).
- `SafetyFilter` يُنقّح ردود الـ LLM من أي تشخيصات طبية قبل إرسالها.
- لا تُخزَّن محادثات الأهل في قاعدة البيانات في النسخة الحالية.

---

## إضافة game_type جديد

عند إضافة نوع لعبة جديد إلى النظام:

1. أضف المهارة إلى `AI/chatbot/rules/skill_rules.json`.
2. أضف `"game_type" => "skill_id"` في `ResultContextBuilder::$gameTypeToSkillId`.
3. أضف أمثلة الأخطاء في `ResultContextBuilder::getMistakeExamples()`.
