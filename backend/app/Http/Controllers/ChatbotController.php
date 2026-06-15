<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Chatbot\ChatbotService;
use App\Services\Chatbot\ResultContextBuilder;

/**
 * ChatbotController
 *
 * Exposes three API endpoints for the Bowsla Arabic chatbot:
 *
 *   POST /api/chatbot/ask
 *   POST /api/chatbot/explain-result
 *   POST /api/chatbot/recommend-exercises
 *
 * All endpoints are protected by auth:sanctum middleware.
 * All responses are in Arabic.
 *
 * Integration path: copy to backend/app/Http/Controllers/ChatbotController.php
 * Then add routes from AI/chatbot/integration/api_routes_snippet.php to
 * backend/routes/api.php
 */
class ChatbotController extends Controller
{
    private ChatbotService $chatbotService;
    private ResultContextBuilder $contextBuilder;

    public function __construct(
        ChatbotService $chatbotService,
        ResultContextBuilder $contextBuilder
    ) {
        $this->chatbotService = $chatbotService;
        $this->contextBuilder = $contextBuilder;
    }

    // ─────────────────────────────────────────────
    // POST /api/chatbot/ask
    // ─────────────────────────────────────────────

    /**
     * Answer a free-text question from a parent.
     *
     * Request body:
     * {
     *   "child_id": 1,          // optional — if provided, context is injected
     *   "message": "يعني إيه انعكاس مرآتي؟"
     * }
     *
     * Response:
     * {
     *   "answer": "..."
     * }
     */
    public function ask(Request $request): JsonResponse
    {
        $request->validate([
            'message'  => 'required|string|max:1000',
            'child_id' => 'nullable|integer|exists:children,id',
        ]);

        $message = trim($request->input('message'));

        // Build child context if child_id provided
        $childContext = [];
        if ($request->filled('child_id')) {
            $childContext = $this->contextBuilder->buildContext(
                $request->input('child_id'),
                $request->user()->id
            ) ?? [];
        }

        $result = $this->chatbotService->ask($message, $childContext);

        return response()->json([
            'status' => 'success',
            'answer' => $result['answer'],
        ]);
    }

    // ─────────────────────────────────────────────
    // POST /api/chatbot/explain-result
    // ─────────────────────────────────────────────

    /**
     * Generate a full Arabic explanation of the child's assessment result.
     *
     * Request body:
     * {
     *   "child_id": 1,
     *   "result_id": 15    // optional — if omitted, uses latest game result
     * }
     *
     * Response:
     * {
     *   "answer": "بناءً على أداء الطفل..."
     * }
     */
    public function explainResult(Request $request): JsonResponse
    {
        $request->validate([
            'child_id'  => 'required|integer|exists:children,id',
            'result_id' => 'nullable|integer|exists:game_results,id',
        ]);

        $childId  = $request->input('child_id');
        $resultId = $request->input('result_id');
        $userId   = $request->user()->id;

        // Build context
        if ($resultId) {
            $childContext = $this->contextBuilder->buildContextForResult($childId, $resultId, $userId);
        } else {
            $childContext = $this->contextBuilder->buildContext($childId, $userId);
        }

        if (!$childContext) {
            return response()->json([
                'status'  => 'error',
                'message' => 'لا يمكن الوصول إلى بيانات الطفل. تأكد من صحة البيانات المُرسَلة.',
            ], 403);
        }

        // Verify child has results to explain
        if (empty($childContext['skills'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'لا توجد نتائج كافية لهذا الطفل بعد. أكمل الاختبارات أولاً.',
            ], 422);
        }

        $result = $this->chatbotService->explainResult($childContext);

        return response()->json([
            'status' => 'success',
            'answer' => $result['answer'],
        ]);
    }

    // ─────────────────────────────────────────────
    // POST /api/chatbot/recommend-exercises
    // ─────────────────────────────────────────────

    /**
     * Return exercise recommendations for the child based on their result.
     *
     * Request body:
     * {
     *   "child_id": 1,
     *   "result_id": 15    // optional
     * }
     *
     * Response:
     * {
     *   "answer": "...",
     *   "recommended_exercises": [
     *     {
     *       "skill_id": "dyslexia_mirroring",
     *       "arabic_name": "الانعكاس المرآتي...",
     *       "training_level": 1,
     *       "exercises": [{ "name": "...", "description": "..." }]
     *     }
     *   ]
     * }
     */
    public function recommendExercises(Request $request): JsonResponse
    {
        $request->validate([
            'child_id'  => 'required|integer|exists:children,id',
            'result_id' => 'nullable|integer|exists:game_results,id',
        ]);

        $childId  = $request->input('child_id');
        $resultId = $request->input('result_id');
        $userId   = $request->user()->id;

        if ($resultId) {
            $childContext = $this->contextBuilder->buildContextForResult($childId, $resultId, $userId);
        } else {
            $childContext = $this->contextBuilder->buildContext($childId, $userId);
        }

        if (!$childContext) {
            return response()->json([
                'status'  => 'error',
                'message' => 'لا يمكن الوصول إلى بيانات الطفل.',
            ], 403);
        }

        if (empty($childContext['skills'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'لا توجد نتائج كافية لتقديم توصيات تمارين. أكمل الاختبارات أولاً.',
            ], 422);
        }

        $result = $this->chatbotService->recommendExercises($childContext);

        return response()->json([
            'status'                 => 'success',
            'answer'                 => $result['answer'],
            'recommended_exercises'  => $result['recommended_exercises'],
        ]);
    }
}
