<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
use App\Http\Controllers\QuestionnaireController;

// مسار لاستقبال بيانات الاستبيان
Route::post('/submit-questionnaire', [QuestionnaireController::class, 'store'])->name('questionnaire.store');