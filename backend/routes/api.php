<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// استدعاء كافة الكنترولرات
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\QuestionnaireController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LearningDifficultyController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TrainingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ==========================================
// أولاً: المسارات العامة (Public - بدون Token)
// ==========================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

// معلومات الصعوبات (متاحة للكل)
Route::get('/difficulties', [LearningDifficultyController::class, 'index']);
Route::get('/difficulties/{id}/questions', [LearningDifficultyController::class, 'getQuestions']);


// ==========================================
// ثانياً: المسارات المحمية (auth:sanctum - تتطلب Token)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {

    // --- 1. إدارة الملف الشخصي للأب ---
    Route::prefix('user')->group(function () {
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile/update', [ProfileController::class, 'update']);
        Route::put('/profile/password', [ProfileController::class, 'updatePassword']);
        Route::post('/upload-image', [MediaController::class, 'uploadUserProfileImage']);
    });

    // --- 2. إدارة الأطفال ---
    // 💡 خليناها children عشان تشتغل مع بوستمان زي ما إنت جربت بالظبط
    Route::get('/children', [ChildController::class, 'index']); // 💡 ده اللي بيعرض كل الأطفال
    Route::post('/children', [ChildController::class, 'store']);
    Route::post('/child/{child_id}/upload-image', [MediaController::class, 'uploadChildImage']);

    // --- 3. الاستبيانات ---
    Route::post('/submit-questionnaire', [QuestionnaireController::class, 'store']);
    Route::get('/results/{child_id}', [QuestionnaireController::class, 'showResults']);
    Route::get('/child/{child_id}/history', [QuestionnaireController::class, 'getChildHistory']);

    // --- 4. نظام التقييم والتشخيص (Assessment) ---
    // 💡 ضفنا راوت التقييم اللي كان ناقص
    Route::get('/assessment-content/{difficulty_id}', [GameController::class, 'getAssessmentContent']);
    Route::post('/submit-game-result', [GameController::class, 'submitGameResult']);

    // --- 5. نظام التدريب اليومي (Training) ---
    // 💡 دخلناهم جوه الحماية عشان محدش يلعب في التدريب غير الطفل المسجل
    Route::get('/training/roadmap/{child_id}', [TrainingController::class, 'getTrainingRoadmap']);
    Route::get('/game-content/{difficulty_id}/{level}', [GameController::class, 'getGameContent']); 
    Route::post('/training/complete', [TrainingController::class, 'completeTrainingLevel']);

    // --- 6. التقارير ---
    Route::get('/child/{child_id}/report', [ReportController::class, 'getComprehensiveReport']);

    // --- 7. لوحة تحكم الإدارة (Admin) ---
    Route::prefix('admin')->group(function () {
        Route::get('/stats', [AdminController::class, 'getStats']); // 💡 تم التجميع هنا
        Route::get('/questions', [QuestionController::class, 'index']);
        Route::post('/questions', [QuestionController::class, 'store']);
        Route::put('/questions/{id}', [QuestionController::class, 'update']);
        Route::delete('/questions/{id}', [QuestionController::class, 'destroy']);
    });
});