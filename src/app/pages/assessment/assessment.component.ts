import { Component, OnDestroy, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { Subject } from 'rxjs';
import { takeUntil } from 'rxjs/operators';
import { AssessmentOption, AssessmentQuestion, AssessmentService } from '@/core/services/assessment.service';
import { getMathRepresentation } from '@/core/shared/constants/math-visual';
import { EMOJI_MAP } from '@/core/shared/constants/emoji-map';

type OptionId = number | string;
type SelectedAnswer = OptionId | OptionId[] | null;
type QuestionType = 'multiple_choice' | 'true_false' | 'matching' | 'ordering' | 'audio' | 'visual' | string;

interface QuestionOption {
    id: OptionId;
    text: string;
    image?: string;
}

interface Question {
    id: number;
    question: string;
    type: QuestionType;
    options: QuestionOption[];
    correct_answer?: OptionId | OptionId[];
    time_limit: number;
    explanation?: string;
    points: number;
    category?: string;
    audio_url?: string;
    image_url?: string;
}

interface SavedAnswer {
    selected: SelectedAnswer;
    correct?: OptionId | OptionId[];
    isCorrect: boolean;
    points: number;
    timeTaken: number;
}

interface AssessmentState {
    currentQuestionIndex: number;
    answers: Record<number, SavedAnswer>;
    score: number;
    timeElapsed: number;
}

interface CategoryAssessmentSummary {
    correct_count: number;
    total_questions: number;
    percentage: number;
}

interface AssessmentDiagnosisSummary {
    diagnosis_type: 'reading_difficulty' | 'math_difficulty' | 'both_difficulties' | 'no_significant_difficulty';
    recommendation: string;
    reading: CategoryAssessmentSummary;
    math: CategoryAssessmentSummary;
}

@Component({
    selector: 'app-assessment',
    standalone: true,
    imports: [CommonModule, FormsModule],
    templateUrl: './assessment.component.html',
    styleUrls: ['./assessment.component.css']
})
export class AssessmentComponent implements OnInit, OnDestroy {
    // -------- UI-only additions to match training-game template (logic unchanged) --------
    stars = 0;
    showStarReward = false;
    helperMessages = [
        'هيا نلعب معاً 🎮',
        'أنت ذكي جداً 🌟',
        'اختر الإجابة الصحيحة 🧠',
        'أحسنت! أكمل التحدي 🚀',
        'لنرَ إن كنت تستطيع حلها 😎'
    ];
    // helperMessage will be provided via UI getter below
    // helperMessage = '';

    readonly circleCircumference = 2 * Math.PI * 52;

    // mapping names expected by assessment.component.html
    get formattedTime(): string { return this.formatTime(this.timeRemaining); }
    get progressPercent(): number { return this.getProgress(); }
    getCircleOffset(percent: number): number { return this.circleCircumference * (1 - percent / 100); }

    get isLastQuestionUI(): boolean { return this.state.currentQuestionIndex === this.questions.length - 1; }


    goBack(): void { this.navigateToQuestionnaire(); }
    next(): void { this.nextQuestion(); }

    // used by template
    get diagnosisLabelUI(): string { return this.diagnosisLabel; }

    get helperMessage(): string {
        return this.getResultMessage();
    }







    // ---------------------------------------------------------------------------------------

    questions: Question[] = [];
    state: AssessmentState = {


        currentQuestionIndex: 0,
        answers: {},
        score: 0,
        timeElapsed: 0
    };

    loading = true;
    errorMessage = '';
    submitted = false;
    submittingResult = false;
    resultSaved = false;
    resultErrorMessage = '';
    difficultyId = 0;
    difficulty_level: any;
    currentLevel: number = 0;
    childId = 0;
    currentQuestion: Question | null = null;
    selectedOption: SelectedAnswer = null;
    timeRemaining = 30;
    totalScore = 0;
    maxScore = 0;
    scorePercentage = 0;
    correctAnswersCount = 0;
    assessmentDiagnosis: AssessmentDiagnosisSummary | null = null;
    readingScorePercentage = 0;
    mathScorePercentage = 0;
    difficultyName = '';
    greetingMessage = 'مرحباً يا بطل!';
    totalTime = 0;
    playingAudio = false;
    reviewMode = false;
    getMathRepresentation = getMathRepresentation;    private readonly defaultQuestionTime = 30;
    private readonly defaultQuestionPoints = 10;
    private destroy$ = new Subject<void>();
    private timerInterval: ReturnType<typeof setInterval> | null = null;
    private currentAudio: HTMLAudioElement | null = null;

    constructor(
        private assessmentService: AssessmentService,
        public router: Router,
        private route: ActivatedRoute
    ) { }

    ngOnInit(): void {
        this.loadLastAssessmentIfExists();
    }

    loadAssessment(): void {
        console.log('[Assessment] loadAssessment() called');
        const difficultyId = this.getNumericQueryParam('difficultyId') ?? this.getStoredDifficultyId();
        const childId = this.getNumericQueryParam('childId') ?? this.getStoredChildId();
        if (!difficultyId || !childId) {
            this.errorMessage = 'معرّفات الطفل أو مستوى الصعوبة غير صحيحة';
            this.loading = false;
            return;
        }
        this.difficultyId = difficultyId;
        this.childId = childId;
        console.log('Loading assessment for Child:', this.childId, 'Difficulty:', this.difficultyId);
        this.storeCurrentSelection();

        this.assessmentService
            .getAssessmentContent(this.difficultyId)
            .pipe(takeUntil(this.destroy$))
            .subscribe({
                next: (response) => {
                    console.log('FULL RESPONSE', response);
                    const content = response.data;
                    const selectedLevel = Array.isArray(content) ? content[0] : (content as any);
                    this.currentLevel = selectedLevel?.difficulty_level ?? 0;
                    console.log('assessment response.data =', content);
                    console.log('selectedLevel =', selectedLevel);
                    console.log('first question =', selectedLevel?.questions?.[0]);

                    this.questions = (selectedLevel?.questions ?? []).map((question: any, index: number) => this.normalizeQuestion(question, index));
                    this.difficultyName = selectedLevel?.level_name || 'التقييم المبدئي';
                    this.totalTime = this.questions.reduce((sum, question) => sum + question.time_limit, 0);
                    this.maxScore = this.questions.reduce((sum, question) => sum + question.points, 0);


                    if (!this.questions.length) {
                        this.errorMessage = 'لا توجد أسئلة متاحة لهذا التقييم حالياً';
                        this.loading = false;
                        return;
                    }

                    this.loading = false;
                    this.setCurrentQuestion(0);
                },
                error: (error) => {
                    this.errorMessage = error?.error?.message || 'فشل تحميل أسئلة التقييم';
                    this.loading = false;
                }
            });
    }

    private loadLastAssessmentIfExists(): void {
        const difficultyId = this.getNumericQueryParam('difficultyId') ?? this.getStoredDifficultyId();
        const childId = this.getNumericQueryParam('childId') ?? this.getStoredChildId();

        if (!difficultyId || !childId) {
            this.loadAssessment();
            return;
        }


        this.difficultyId = difficultyId;
        this.childId = childId;
        this.storeCurrentSelection();

        this.submitted = false;
        this.loading = true;
        this.assessmentService
            .getAssessmentResult(this.childId, String(this.difficultyId))
            .pipe(takeUntil(this.destroy$))
            .subscribe({
                next: (res) => {
                    const data = res?.data;
                    if (data?.raw_score !== undefined && data?.raw_score !== null) {
                        this.submitted = true;
                        this.reviewMode = true;
                        this.loading = false;
                        this.totalScore = Number(data.raw_score) || 0;

                        this.correctAnswersCount = Number(data.correct_count) || 0;

                        const totalQuestions = Number(data.total_questions) || 0;

                        this.scorePercentage =
                            totalQuestions > 0
                                ? Math.round((this.correctAnswersCount / totalQuestions) * 100)
                                : 0;
                        this.assessmentDiagnosis = data?.diagnosis ?? this.assessmentDiagnosis;
                        this.totalTime = 0;
                        return;
                    }
                    this.loadAssessment();
                },

                error: () => {
                    this.loadAssessment();
                }
            });
    }

    getOptionEmoji(text: string): string {

        return EMOJI_MAP[text] || '✨';

    }

    isLetterOption(text: string): boolean {

        return text.length === 1;

    }

    ngOnDestroy(): void {
        this.destroy$.next();
        this.destroy$.complete();
        this.stopTimer();
        this.stopAudio();
    }

    selectOption(optionId: OptionId): void {
        if (this.reviewMode) {
            return;
        }
        this.selectedOption = optionId;
    }

    isSelected(optionId: OptionId): boolean {
        if (Array.isArray(this.selectedOption)) {
            return this.selectedOption.some((selectedId) => this.isSameValue(selectedId, optionId));
        }

        return this.selectedOption !== null && this.isSameValue(this.selectedOption, optionId);
    }

    nextQuestion(): void {
        this.saveCurrentAnswer();
        this.stopTimer();

        if (this.isLastQuestion()) {
            this.finishAssessment();
            return;
        }

        this.setCurrentQuestion(this.state.currentQuestionIndex + 1);
    }

    previousQuestion(): void {
        if (!this.canGoPrevious()) {
            return;
        }

        this.saveCurrentAnswer();
        this.stopTimer();
        this.setCurrentQuestion(this.state.currentQuestionIndex - 1);
    }

    moveOrderingOption(index: number, direction: -1 | 1): void {
        if (!this.currentQuestion || !this.isOrderingQuestion(this.currentQuestion)) {
            return;
        }

        const currentOrder = this.getSelectedOrder();
        const targetIndex = index + direction;

        if (targetIndex < 0 || targetIndex >= currentOrder.length) {
            return;
        }

        const movedItem = currentOrder[index];
        currentOrder[index] = currentOrder[targetIndex];
        currentOrder[targetIndex] = movedItem;
        this.selectedOption = currentOrder;
    }

    canMoveOrderingOption(index: number, direction: -1 | 1): boolean {
        if (!this.currentQuestion || !this.isOrderingQuestion(this.currentQuestion)) {
            return false;
        }

        const targetIndex = index + direction;
        return targetIndex >= 0 && targetIndex < this.currentQuestion.options.length;
    }

    canGoPrevious(): boolean {
        return this.state.currentQuestionIndex > 0;
    }

    isLastQuestion(): boolean {
        return this.state.currentQuestionIndex === this.questions.length - 1;
    }

    getProgress(): number {
        if (!this.questions.length) {
            return 0;
        }

        return Math.round(((this.state.currentQuestionIndex + 1) / this.questions.length) * 100);
    }

    formatTime(seconds: number): string {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }

    playAudio(url: string): void {
        if (!url) {
            return;
        }

        this.stopAudio();
        this.currentAudio = new Audio(url);
        this.playingAudio = true;

        this.currentAudio.onended = () => {
            this.playingAudio = false;
            this.currentAudio = null;
        };

        this.currentAudio.onerror = () => {
            this.playingAudio = false;
            this.currentAudio = null;
        };

        this.currentAudio.play().catch(() => {
            this.playingAudio = false;
            this.currentAudio = null;
        });
    }

    isChoiceQuestion(question: Question): boolean {
        return ['multiple_choice', 'true_false', 'audio'].includes(question.type) || !this.isSpecialQuestion(question);
    }

    isVisualQuestion(question: Question): boolean {
        return question.type === 'visual' || question.type === 'matching';
    }

    isOrderingQuestion(question: Question): boolean {
        return question.type === 'ordering';
    }

    get orderedOptions(): QuestionOption[] {
        if (!this.currentQuestion) {
            return [];
        }

        if (!this.isOrderingQuestion(this.currentQuestion)) {
            return this.currentQuestion.options;
        }

        const selectedOrder = this.getSelectedOrder();
        const optionsById = new Map(this.currentQuestion.options.map((option) => [String(option.id), option]));
        const orderedOptions = selectedOrder.map((optionId) => optionsById.get(String(optionId))).filter((option): option is QuestionOption => Boolean(option));
        const missingOptions = this.currentQuestion.options.filter((option) => !selectedOrder.some((optionId) => this.isSameValue(optionId, option.id)));

        return [...orderedOptions, ...missingOptions];
    }

    getNextButtonText(): string {
        return this.isLastQuestion() ? 'إنهاء التقييم' : 'السؤال التالي';
    }

    getResultMessage(): string {
        if (this.assessmentDiagnosis) {
            return this.assessmentDiagnosis.recommendation;
        }

        if (this.scorePercentage >= 80) {
            return 'ممتاز جداً! أداء الطفل رائع.';
        }

        if (this.scorePercentage >= 50) {
            return 'جيد! يوجد تقدم ملحوظ ويمكن تحسينه بالتدريب.';
        }

        return 'يحتاج الطفل إلى تدريب إضافي ومتابعة مستمرة.';
    }

    onShowQuestions(): void {
        this.reviewMode = true;
        this.submitted = false;
        this.loading = true;
        this.errorMessage = '';
        this.loadAssessment();
    }

    retrySubmitResult(): void {
        this.submitAssessmentResult();
    }


    navigateToQuestionnaire(): void {
        this.router.navigate(['/questionnaire', this.childId || 1], {
            queryParams: {
                childId: this.childId || null,
                difficultyId: this.difficultyId || null
            }
        });
    }

    navigateToTraining(): void {
        this.router.navigate(['/training'], {
            queryParams: {
                childId: this.childId,
                difficultyId: this.difficultyId
            }
        });
    }

    private normalizeQuestion(rawQuestion: AssessmentQuestion, questionIndex: number): Question {
        const options = (rawQuestion.options ?? []).map((option, optionIndex) => this.normalizeOption(option, optionIndex));
        const timeLimit = this.toPositiveNumber(rawQuestion.time_limit, this.defaultQuestionTime);
        const points = this.toPositiveNumber(rawQuestion.points, this.defaultQuestionPoints);

        return {
            id: rawQuestion.id ?? questionIndex + 1,
            question: rawQuestion.question || (rawQuestion as any).target || (rawQuestion as any).text || `السؤال ${questionIndex + 1}`,
            type: rawQuestion.type || 'multiple_choice',
            options,
            correct_answer: this.resolveCorrectAnswer(rawQuestion.correct_answer, options),
            time_limit: timeLimit,
            explanation: rawQuestion.explanation,
            points,
            category: rawQuestion.category,
            audio_url: rawQuestion.audio_url,
            image_url: rawQuestion.image_url
        };
    }

    private normalizeOption(option: string | AssessmentOption, optionIndex: number): QuestionOption {
        if (typeof option === 'string') {
            return {
                id: optionIndex + 1,
                text: option
            };
        }

        return {
            id: option.id ?? optionIndex + 1,
            text: option.text || String(option.id ?? `اختيار ${optionIndex + 1}`),
            image: option.image
        };
    }

    private resolveCorrectAnswer(correctAnswer: number | string | Array<number | string> | null | undefined, options: QuestionOption[]): OptionId | OptionId[] | undefined {
        if (correctAnswer === undefined || correctAnswer === null || correctAnswer === '') {
            return undefined;
        }

        if (Array.isArray(correctAnswer)) {
            return correctAnswer.map((answer) => this.resolveSingleCorrectAnswer(answer, options));
        }

        return this.resolveSingleCorrectAnswer(correctAnswer, options);
    }

    private resolveSingleCorrectAnswer(answer: number | string, options: QuestionOption[]): OptionId {
        const matchedById = options.find((option) => this.isSameValue(option.id, answer));
        if (matchedById) {
            return matchedById.id;
        }

        const matchedByText = options.find((option) => this.isSameValue(option.text, answer));
        return matchedByText?.id ?? answer;
    }

    private setCurrentQuestion(questionIndex: number): void {
        this.state.currentQuestionIndex = questionIndex;
        this.currentQuestion = this.questions[questionIndex] ?? null;
        this.selectedOption = this.getSavedSelection(this.currentQuestion);
        this.startTimer();
    }

    private getSavedSelection(question: Question | null): SelectedAnswer {
        if (!question) {
            return null;
        }

        const savedAnswer = this.state.answers[question.id]?.selected;
        if (savedAnswer !== undefined) {
            return savedAnswer;
        }

        if (this.isOrderingQuestion(question)) {
            return question.options.map((option) => option.id);
        }

        return null;
    }

    private startTimer(): void {
        if (!this.currentQuestion) {
            return;
        }

        this.stopTimer();
        this.timeRemaining = this.currentQuestion.time_limit;

        this.timerInterval = setInterval(() => {
            this.timeRemaining -= 1;
            this.state.timeElapsed += 1;

            if (this.timeRemaining <= 0) {
                this.timeRemaining = 0;
                this.stopTimer();
            }
        }, 1000);
    }

    private stopTimer(): void {
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
            this.timerInterval = null;
        }
    }

    private stopAudio(): void {
        if (this.currentAudio) {
            this.currentAudio.pause();
            this.currentAudio.currentTime = 0;
            this.currentAudio = null;
        }

        this.playingAudio = false;
    }

    private saveCurrentAnswer(): void {
        if (!this.currentQuestion) {
            return;
        }

        const selectedAnswer = this.getCurrentSelectedAnswer();
        const isCorrect = selectedAnswer !== null && this.isAnswerCorrect(selectedAnswer, this.currentQuestion);
        const points = isCorrect ? this.currentQuestion.points : 0;

        this.state.answers[this.currentQuestion.id] = {
            selected: selectedAnswer,
            correct: this.currentQuestion.correct_answer,
            isCorrect,
            points,
            timeTaken: Math.max(this.currentQuestion.time_limit - this.timeRemaining, 0)
        };

        this.recalculateScore();
        this.totalScore = this.state.score;

        console.log(`[Assessment] Question ${this.currentQuestion.id} Answered. Correct: ${isCorrect}, Points: ${points}. Total Score now: ${this.totalScore}`);
    }

    private getCurrentSelectedAnswer(): SelectedAnswer {
        if (!this.currentQuestion) {
            return null;
        }

        if (this.isOrderingQuestion(this.currentQuestion)) {
            return this.getSelectedOrder();
        }

        return this.selectedOption;
    }

    private getSelectedOrder(): OptionId[] {
        if (!this.currentQuestion) {
            return [];
        }

        if (Array.isArray(this.selectedOption)) {
            return [...this.selectedOption];
        }

        return this.currentQuestion.options.map((option) => option.id);
    }

    private isAnswerCorrect(selectedAnswer: Exclude<SelectedAnswer, null>, question: Question): boolean {
        const correctAnswer = question.correct_answer;
        if (correctAnswer === undefined) {
            return false;
        }

        if (Array.isArray(correctAnswer)) {
            if (!Array.isArray(selectedAnswer) || selectedAnswer.length !== correctAnswer.length) {
                return false;
            }

            if (this.isOrderingQuestion(question)) {
                return correctAnswer.every((answer, index) => this.isSameValue(answer, selectedAnswer[index]));
            }

            return correctAnswer.every((answer) =>
                selectedAnswer.some((selected) => this.isSameValue(answer, selected))
            );
        }

        return !Array.isArray(selectedAnswer) && this.isSameValue(selectedAnswer, correctAnswer);
    }

    private recalculateScore(): void {
        this.state.score = Object.values(this.state.answers).reduce((sum, answer) => sum + answer.points, 0);
    }

    private finishAssessment(): void {
        this.stopTimer();
        this.recalculateScore();

        this.submitted = true;
        this.totalScore = this.state.score;
        this.correctAnswersCount = Object.values(this.state.answers).filter((answer) => answer.isCorrect).length;

        this.scorePercentage = this.maxScore > 0 ? Math.round((this.totalScore / this.maxScore) * 100) : 0;

        this.assessmentDiagnosis = this.calculateAssessmentDiagnosis();
        this.readingScorePercentage =
            this.assessmentDiagnosis.reading.percentage;

        this.mathScorePercentage =
            this.assessmentDiagnosis.math.percentage;

        this.submitAssessmentResult();
    }


    private submitAssessmentResult(): void {
        const queryChildId = this.getNumericQueryParam('childId');
        const queryDifficultyId = this.getNumericQueryParam('difficultyId');

        const storedChildId = this.getStoredChildId();
        const storedDifficultyId = this.getStoredDifficultyId();

        this.childId = queryChildId ?? storedChildId ?? this.childId ?? 0;
        this.difficultyId = queryDifficultyId ?? storedDifficultyId ?? this.difficultyId ?? 0;

        const finalScoreToSend =
            this.questions.length > 0
                ? Math.round((this.correctAnswersCount / this.questions.length) * 100)
                : 0;
        const diagnosis = this.assessmentDiagnosis ?? this.calculateAssessmentDiagnosis();

        console.log('[Assessment] Sending API Request -> submitAssessmentResult', {
            child_id: this.childId,
            game_type: String(this.difficultyId),
            raw_score: finalScoreToSend,
            reading_correct_count: diagnosis.reading.correct_count,
            reading_total_questions: diagnosis.reading.total_questions,
            reading_percentage: diagnosis.reading.percentage,
            math_correct_count: diagnosis.math.correct_count,
            math_total_questions: diagnosis.math.total_questions,
            math_percentage: diagnosis.math.percentage
        });

        if (!this.childId || !this.difficultyId) {
            console.warn('[Assessment] submitAssessmentResult blocked: missing childId or difficultyId');
            return;
        }

        this.submittingResult = true;
        this.resultSaved = false;
        this.resultErrorMessage = '';

        const payload: any = {
            child_id: this.childId,
            game_type: this.getGameType(),
            raw_score: finalScoreToSend,
            session_type: 'assessment',
            learning_difficulty_id: this.difficultyId,
            difficulty_level: this.currentLevel,
            correct_count: this.correctAnswersCount,
            total_questions: this.questions.length,
            reading_correct_count: diagnosis.reading.correct_count,
            reading_total_questions: diagnosis.reading.total_questions,
            reading_percentage: diagnosis.reading.percentage,
            math_correct_count: diagnosis.math.correct_count,
            math_total_questions: diagnosis.math.total_questions,
            math_percentage: diagnosis.math.percentage,
        };

        this.assessmentService
            .submitAssessmentResult(payload)
            .pipe(takeUntil(this.destroy$))
            .subscribe({
                next: (res) => {
                    this.submittingResult = false;
                    this.resultSaved = true;
                    this.assessmentDiagnosis = res?.diagnosis ?? this.assessmentDiagnosis;
                    if (this.assessmentDiagnosis) {

                        this.readingScorePercentage =
                            this.assessmentDiagnosis.reading?.percentage ?? 0;

                        this.mathScorePercentage =
                            this.assessmentDiagnosis.math?.percentage ?? 0;
                    }
                },
                error: (error) => {
                    this.submittingResult = false;
                    this.resultSaved = false;
                    this.resultErrorMessage = error?.error?.message || 'تم عرض النتيجة، لكن تعذر حفظها على الخادم';
                }
            });
    }

    private getGameType(): string {
        const map: Record<number, string> = {
            1: 'visual_discrimination',
            2: 'number_direction'
        };

        return map[this.difficultyId] ?? 'unknown';
    }

    private calculateAssessmentDiagnosis(): AssessmentDiagnosisSummary {
        const weakThreshold = 60;
        const readingQuestions = this.questions.filter((question) => (question.category ?? '').toLowerCase() === 'reading');
        const mathQuestions = this.questions.filter((question) => (question.category ?? '').toLowerCase() === 'math');

        const readingCorrect = readingQuestions.filter((question) => this.state.answers[question.id]?.isCorrect).length;
        const mathCorrect = mathQuestions.filter((question) => this.state.answers[question.id]?.isCorrect).length;

        const readingSummary = this.buildCategorySummary(readingCorrect, readingQuestions.length);
        const mathSummary = this.buildCategorySummary(mathCorrect, mathQuestions.length);

        const readingWeak = readingSummary.total_questions > 0 && readingSummary.percentage < weakThreshold;
        const mathWeak = mathSummary.total_questions > 0 && mathSummary.percentage < weakThreshold;

        let diagnosisType: AssessmentDiagnosisSummary['diagnosis_type'] = 'no_significant_difficulty';
        let recommendation = 'لا توجد مؤشرات واضحة على صعوبة محددة حاليًا.';

        if (readingWeak && mathWeak) {
            diagnosisType = 'both_difficulties';
            recommendation = 'يوصى بالاستمرار في تدريبات القراءة والحساب';
        } else if (readingWeak) {
            diagnosisType = 'reading_difficulty';
            recommendation = 'يوصى بالاستمرار في تدريبات القراءة';
        } else if (mathWeak) {
            diagnosisType = 'math_difficulty';
            recommendation = 'يوصى بالاستمرار في تدريبات الحساب';
        }

        return {
            diagnosis_type: diagnosisType,
            recommendation,
            reading: readingSummary,
            math: mathSummary,
        };
    }

    private buildCategorySummary(correctCount: number, totalQuestions: number): CategoryAssessmentSummary {
        return {
            correct_count: correctCount,
            total_questions: totalQuestions,
            percentage: totalQuestions > 0 ? Math.round((correctCount / totalQuestions) * 100) : 0,
        };
    }

    private isSpecialQuestion(question: Question): boolean {
        return this.isVisualQuestion(question) || this.isOrderingQuestion(question);
    }

    private isSameValue(firstValue: OptionId, secondValue: OptionId): boolean {
        return String(firstValue) === String(secondValue);
    }

    private toPositiveNumber(value: unknown, fallback: number): number {
        const numericValue = Number(value);
        return Number.isFinite(numericValue) && numericValue > 0 ? numericValue : fallback;
    }

    private getNumericQueryParam(paramName: string): number | null {
        const value = Number(this.route.snapshot.queryParamMap.get(paramName));
        return Number.isFinite(value) && value > 0 ? value : null;
    }

    private getStoredChildId(): number | null {
        const fromSelection = this.getLocalStorageNumber('selected_child_id') ?? this.getLocalStorageNumber('child_id');
        if (fromSelection) {
            return fromSelection;
        }

        return this.getQuestionnaireResultNumber(['child_id', 'childId'], ['data.child_id', 'result.child_id']);
    }

    private getStoredDifficultyId(): number | null {
        const fromSelection = this.getLocalStorageNumber('selected_difficulty_id') ?? this.getLocalStorageNumber('difficulty_id');
        if (fromSelection) {
            return fromSelection;
        }

        return this.getQuestionnaireResultNumber(['difficulty_id', 'difficultyId', 'learning_difficulty_id'], ['data.difficulty_id', 'data.learning_difficulty_id', 'result.difficulty_id', 'result.learning_difficulty_id']);
    }

    private getLocalStorageNumber(key: string): number | null {
        try {
            const rawValue = localStorage.getItem(key);
            const value = Number(rawValue);
            return Number.isFinite(value) && value > 0 ? value : null;
        } catch {
            return null;
        }
    }

    private getQuestionnaireResultNumber(rootKeys: string[], nestedPaths: string[]): number | null {
        try {
            const rawResult = localStorage.getItem('questionnaireResult');
            if (!rawResult) {
                return null;
            }

            const result = JSON.parse(rawResult) as Record<string, unknown>;
            const paths = [...rootKeys, ...nestedPaths];

            for (const path of paths) {
                const value = this.readPath(result, path);
                const numericValue = Number(value);

                if (Number.isFinite(numericValue) && numericValue > 0) {
                    return numericValue;
                }
            }

            return null;
        } catch {
            return null;
        }
    }

    private readPath(source: Record<string, unknown>, path: string): unknown {
        return path.split('.').reduce<unknown>((currentValue, key) => {
            if (currentValue && typeof currentValue === 'object' && key in currentValue) {
                return (currentValue as Record<string, unknown>)[key];
            }

            return undefined;
        }, source);
    }

    private storeCurrentSelection(): void {
        try {
            localStorage.setItem('selected_child_id', String(this.childId));
            localStorage.setItem('selected_difficulty_id', String(this.difficultyId));
        } catch {
            return;
        }
    }

    get readingPercentage(): number {
        return this.readingScorePercentage ?? 0;
    }

    get mathPercentage(): number {
        return this.mathScorePercentage ?? 0;
    }

    get readingStatus(): string {
        if (this.readingPercentage >= 70) return 'نقطة قوة';
        if (this.readingPercentage >= 40) return 'فرصة للتطوير';
        return 'تحتاج للدعم';
    }

    get mathStatus(): string {
        if (this.mathPercentage >= 70) return 'نقطة قوة';
        if (this.mathPercentage >= 40) return 'فرصة للتطوير';
        return 'تحتاج للدعم';
    }

    get readingMessage(): string {
        if (this.readingPercentage >= 70) {
            return 'أداء طفلك متميز في مهارات القراءة.';
        }

        if (this.readingPercentage >= 40) {
            return 'يحرز طفلك تقدماً جيداً في القراءة.';
        }

        return 'مهارات القراءة تحتاج إلى دعم إضافي.';
    }

    get mathMessage(): string {
        if (this.mathPercentage >= 70) {
            return 'أداء طفلك جيد جداً في الحساب.';
        }

        if (this.mathPercentage >= 40) {
            return 'يوجد تقدم جيد في مهارات الحساب.';
        }

        return 'مهارات الحساب تحتاج إلى خطة دعم إضافية.';
    }

    get diagnosisLabel(): string {

        switch (this.assessmentDiagnosis?.diagnosis_type) {

            case 'reading_difficulty':
                return 'صعوبة القراءة';

            case 'math_difficulty':
                return 'صعوبة الحساب';

            case 'both_difficulties':
                return 'صعوبات متعددة';

            default:
                return 'لا توجد صعوبات واضحة';
        }
    }
}
