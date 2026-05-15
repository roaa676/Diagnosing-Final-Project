<?php

/**
 * ────────────────────────────────────────────────────────────────────────────
 * CHATBOT API ROUTES SNIPPET — بوصلة / Bowsla
 * ────────────────────────────────────────────────────────────────────────────
 *
 * Add the following block to backend/routes/api.php inside the
 * auth:sanctum middleware group.
 *
 * IMPORTANT: First add this import at the top of api.php:
 *   use App\Http\Controllers\ChatbotController;
 *
 * Then paste the route group below inside the existing:
 *   Route::middleware('auth:sanctum')->group(function () { ... });
 * ────────────────────────────────────────────────────────────────────────────
 */

// ─── DO NOT PASTE THIS FILE DIRECTLY ───
// Only paste the route group block below into backend/routes/api.php

// ════════════════════════════════════════
// 8. مسارات المساعد الذكي (Chatbot)
// ════════════════════════════════════════
Route::prefix('chatbot')->group(function () {

    // سؤال حر من ولي الأمر
    // POST /api/chatbot/ask
    Route::post('/ask', [ChatbotController::class, 'ask']);

    // شرح نتيجة الطفل
    // POST /api/chatbot/explain-result
    Route::post('/explain-result', [ChatbotController::class, 'explainResult']);

    // توصية تمارين للطفل
    // POST /api/chatbot/recommend-exercises
    Route::post('/recommend-exercises', [ChatbotController::class, 'recommendExercises']);
});
