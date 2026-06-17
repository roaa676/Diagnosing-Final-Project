import { CommonModule } from '@angular/common';
import { Component, OnDestroy, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Subject, takeUntil } from 'rxjs';
import { TrainingService } from '@/core/services/training.service';
import { LearningDifficultyService } from '@/core/services/learning-difficulty.service';

type OptionId = number | string;

type QuestionType = 'multiple_choice' | 'true_false' | 'matching' | 'ordering' | 'audio' | 'visual' | string;

interface TrainingQuestionOption {
    id: OptionId;
    text: string;
    image?: string;
}

interface TrainingQuestion {
    id: number;
    question: string;
    type: QuestionType;
    options: TrainingQuestionOption[];
    points?: number;
    time_limit?: number;
    correct_answer?: OptionId | OptionId[];
    audio_url?: string;
    image_url?: string;
}

interface TrainingLevelResponse {
    status: string;
    message?: string;
    data?: {
        questions?: TrainingQuestion[];
    };
    questions?: TrainingQuestion[];
}

@Component({
    selector: 'app-training-game',
    standalone: true,
    imports: [CommonModule],
    templateUrl: './training-game.component.html',
    styleUrls: ['./training-game.component.css']
})
export class TrainingGameComponent implements OnInit, OnDestroy {
    playingAudio = false;
    private currentAudio: HTMLAudioElement | null = null;

    private destroy$ = new Subject<void>();
    readonly circleCircumference = 2 * Math.PI * 52;

    loading = true;
    errorMessage = '';

    questions: TrainingQuestion[] = [];
    currentQuestionIndex = 0;
    selectedOption: OptionId | OptionId[] | null = null;
    answers: Record<number, { selected: OptionId | OptionId[] | null; isCorrect: boolean; points: number }> = {};

    showResult = false;
    finalScore = 0;
    correctCount = 0;
    trainingResults: any[] = [];
    difficultyId!: number;
    level!: number;
    childId!: number;
    testType: string = '';

    constructor(
        private readonly route: ActivatedRoute,
        private readonly router: Router,
        private readonly trainingService: TrainingService,
        private readonly difficultyService: LearningDifficultyService
    ) { }

    ngOnInit(): void {

        this.route.queryParams
            .pipe(takeUntil(this.destroy$))
            .subscribe(params => {

                this.resetTrainingState();

                const difficultyId = Number(params['difficultyId']);
                const level = Number(params['level']);
                const childId = Number(
                    params['childId'] || this.getStoredNumber('child_id')
                );

                if (
                    !Number.isFinite(difficultyId) || difficultyId <= 0 ||
                    !Number.isFinite(level) || level <= 0 ||
                    !Number.isFinite(childId) || childId <= 0
                ) {
                    this.errorMessage = 'معرّفات التدريب غير صحيحة';
                    this.loading = false;
                    return;
                }

                this.difficultyId = difficultyId;
                this.level = level;
                this.childId = childId;

                this.loadDifficultyAndQuestions();
            });
    }
    private resetTrainingState(): void {

        this.loading = true;
        this.errorMessage = '';

        this.questions = [];
        this.answers = {};

        this.currentQuestionIndex = 0;
        this.selectedOption = null;

        this.showResult = false;

        this.finalScore = 0;
        this.correctCount = 0;
    }
    private loadDifficultyAndQuestions(): void {

        this.difficultyService
            .getAllDifficulties()
            .pipe(takeUntil(this.destroy$))
            .subscribe({
                next: (response) => {

                    const difficulty =
                        response.data?.find(
                            (d: any) => d.id === this.difficultyId
                        );

                    if (difficulty?.test_type) {

                        this.testType = difficulty.test_type;

                    } else {

                        if (this.difficultyId === 1) {
                            this.testType = 'visual_discrimination';
                        } else if (this.difficultyId === 2) {
                            this.testType = 'magnitude_comparison';
                        } else {
                            this.testType = 'visual_discrimination';
                        }
                    }

                    this.fetchQuestions();
                },

                error: () => {
                    this.errorMessage = 'فشل تحميل معلومات الصعوبة';
                    this.loading = false;
                }
            });
    }

    ngOnDestroy(): void {
        this.destroy$.next();
        this.destroy$.complete();
        this.stopAudio();
    }

    goBack(): void {
        this.router.navigate(['/training/levels'], {
            queryParams: { childId: this.childId, difficultyId: this.difficultyId }
        });
    }
    get levelName(): string {
        switch (this.level) {
            case 1:
                return 'المستوى الأول';
            case 2:
                return 'المستوى الثاني';
            case 3:
                return 'المستوى الثالث';
            default:
                return `المستوى ${this.level}`;
        }
    }

    get difficultyName(): string {
        switch (this.difficultyId) {
            case 1:
                return 'صعوبة القراءة';
            case 2:
                return 'صعوبة الحساب';
            default:
                return 'التدريب';
        }
    }

    get scorePercentage(): number {
        if (!this.questions.length) {
            return 0;
        }

        return Math.round(
            (this.correctCount / this.questions.length) * 100
        );
    }

    getCircleOffset(percent: number): number {
        return this.circleCircumference * (1 - percent / 100);
    }

    getLevelName(level: number): string {

        switch (level) {

            case 1:
                return 'المستوى الأول';

            case 2:
                return 'المستوى الثاني';

            case 3:
                return 'المستوى الثالث';

            default:
                return `المستوى ${level}`;
        }
    }
    get nextLevelName(): string {
        switch (this.level + 1) {
            case 2:
                return 'المستوى الثاني';
            case 3:
                return 'المستوى الثالث';
            default:
                return '';
        }
    }

    get hasNextLevel(): boolean {
        return this.level < 3;
    }

    get performanceLabel(): string {

        if (this.scorePercentage >= 80) {
            return 'أداء ممتاز';
        }

        if (this.scorePercentage >= 60) {
            return 'أداء جيد';
        }

        return 'يحتاج إلى تدريب إضافي';
    }

    private fetchQuestions(): void {
        this.trainingService
            .getGameContent(this.difficultyId, this.level)
            .pipe(takeUntil(this.destroy$))
            .subscribe({
                next: (res: TrainingLevelResponse) => {
                    const rawQuestions = (res?.data?.questions ?? res?.questions ?? []) as any[];
                    this.questions = this.transformQuestions(rawQuestions);

                    if (!this.questions.length) {
                        this.errorMessage = 'لا توجد أسئلة متاحة لهذا المستوى حالياً';
                        this.loading = false;
                        return;
                    }

                    this.loading = false;
                    this.currentQuestionIndex = 0;
                    this.selectedOption = null;
                    this.stopAudio();
                },
                error: (err) => {
                    this.errorMessage = err?.error?.message || 'فشل تحميل أسئلة التدريب';
                    this.loading = false;
                }
            });
    }

    private transformQuestions(rawQuestions: any[]): TrainingQuestion[] {
        return (rawQuestions || []).map((rq, index) => {
            const baseId = rq.id ?? index + 1;

            if (rq && rq.target && Array.isArray(rq.options)) {
                const options = rq.options.map((opt: any, idx: number) => ({ id: opt, text: String(opt) }));
                const questionText = rq.prompt || `اختر العنصر المطابق لـ ${rq.target}`;
                const correct = rq.correct ?? rq.target;

                return {
                    id: baseId,
                    question: questionText,
                    type: 'multiple_choice',
                    options,
                    correct_answer: correct,
                    points: rq.points ?? 10
                } as TrainingQuestion;
            }

            if (rq && (rq.left !== undefined || rq.right !== undefined)) {
                const leftVal = rq.left ?? '';
                const rightVal = rq.right ?? '';
                const options = [
                    { id: 'left', text: String(leftVal) },
                    { id: 'right', text: String(rightVal) }
                ];
                const questionText = rq.prompt || 'اختر الجانب الصحيح';
                const correct = rq.correct_side ?? rq.correctSide ?? 'left';

                return {
                    id: baseId,
                    question: questionText,
                    type: 'visual',
                    options,
                    correct_answer: correct,
                    points: rq.points ?? 10
                } as TrainingQuestion;
            }

            if (rq && rq.options && rq.question) {
                return {
                    id: rq.id ?? baseId,
                    question: rq.question,
                    type: rq.type ?? 'multiple_choice',
                    options: (rq.options || []).map((o: any) => ({
                        id: typeof o === 'string' ? o : (o.id ?? o.text),
                        text: typeof o === 'string' ? o : o.text
                    })),
                    correct_answer: rq.correct_answer ?? rq.correct
                } as TrainingQuestion;
            }

            return {
                id: baseId,
                question: JSON.stringify(rq),
                type: 'multiple_choice',
                options: [],
                points: 0
            } as TrainingQuestion;
        });
    }

    playAudio(url: string): void {
        if (!url) return;
        this.stopAudio();
        this.currentAudio = new Audio(url);
        this.playingAudio = true;
        this.currentAudio.onended = () => { this.playingAudio = false; this.currentAudio = null; };
        this.currentAudio.onerror = () => { this.playingAudio = false; this.currentAudio = null; };
        this.currentAudio.play().catch(() => { this.playingAudio = false; this.currentAudio = null; });
    }

    stopAudio(): void {
        if (this.currentAudio) {
            this.currentAudio.pause();
            this.currentAudio.currentTime = 0;
            this.currentAudio = null;
        }
        this.playingAudio = false;
    }

    isChoiceQuestion(q: TrainingQuestion): boolean {
        return ['multiple_choice', 'true_false', 'audio'].includes(q.type);
    }

    isVisualQuestion(q: TrainingQuestion): boolean {
        return q.type === 'visual' || q.type === 'matching';
    }

    get currentQuestion(): TrainingQuestion | null {
        return this.questions[this.currentQuestionIndex] ?? null;
    }

    get isLastQuestion(): boolean {
        return this.currentQuestionIndex >= this.questions.length - 1;
    }

    get progressPercent(): number {
        if (!this.questions.length) return 0;
        return Math.round(((this.currentQuestionIndex + 1) / this.questions.length) * 100);
    }

    selectOption(optionId: OptionId): void {
        this.selectedOption = optionId;
    }

    isSelected(optionId: OptionId): boolean {
        if (Array.isArray(this.selectedOption)) {
            return this.selectedOption.some((x) => String(x) === String(optionId));
        }
        return this.selectedOption !== null && String(this.selectedOption) === String(optionId);
    }

    next(): void {
        this.saveCurrentAnswer();

        if (this.isLastQuestion) {
            this.submitAndExit();
            return;
        }

        this.currentQuestionIndex += 1;
        this.selectedOption = this.getSavedSelection(this.currentQuestion);
    }

    private submitAndExit(): void {
        this.saveCurrentAnswer();

        const finalScore = this.calculateTotalScore();

        const correctCount = Object.values(this.answers).filter((a) => a.isCorrect).length;

        console.log('QUESTIONS = ', this.questions);

        console.log('ANSWERS = ', this.answers);

        console.log(
            'CORRECT COUNT = ',
            Object.values(this.answers).filter(a => a.isCorrect).length
        );

        const payload = {
            child_id: this.childId,
            game_type: this.testType,
            raw_score: finalScore,
            session_type: 'training',
            learning_difficulty_id: this.difficultyId,
            difficulty_level: this.level,
            total_questions: this.questions.length,
            correct_count: correctCount,
        };

        this.trainingService
            .submitGameResult(payload)
            .pipe(takeUntil(this.destroy$))
            .subscribe({
                next: () => {
                    this.finalizeResultAndShow(finalScore);
                },
                error: () => {
                    this.finalizeResultAndShow(finalScore);
                }
            });
    }

    private finalizeResultAndShow(finalScore: number): void {
        this.finalScore = finalScore;
        this.correctCount = Object.values(this.answers).filter((a) => a.isCorrect).length;
        this.showResult = true;
        this.loadTrainingResults();
        this.trainingService
            .completeTrainingLevel(
                this.childId,
                this.testType
            )
            .pipe(takeUntil(this.destroy$))
            .subscribe({
                next: () => { },
                error: () => { }
            });
    }

    goToNextLevel(): void {
        const nextLevel = this.level + 1;
        if (nextLevel > 3) {
            this.router.navigate(['/training/levels'], {
                queryParams: { childId: this.childId, difficultyId: this.difficultyId }
            });
            return;
        }

        this.router.navigate(['/training/game'], {
            queryParams: { childId: this.childId, difficultyId: this.difficultyId, level: nextLevel }
        });
    }

    private saveCurrentAnswer(): void {
        if (!this.currentQuestion) {
            return;
        }

        const selectedAnswer = this.getCurrentSelectedAnswer();
        const isCorrect = selectedAnswer !== null && this.isTrainingAnswerCorrect(selectedAnswer, this.currentQuestion);
        console.log('QUESTION = ', this.currentQuestion.question);
        console.log('SELECTED = ', selectedAnswer);
        console.log('CORRECT ANSWER = ', this.currentQuestion.correct_answer);
        console.log('IS CORRECT = ', isCorrect);
        const points = isCorrect ? this.getQuestionPoints(this.currentQuestion) : 0;

        this.answers[this.currentQuestion.id] = {
            selected: selectedAnswer,
            isCorrect,
            points
        };
    }

    private getSavedSelection(question: TrainingQuestion | null): OptionId | OptionId[] | null {
        if (!question) {
            return null;
        }

        const saved = this.answers[question.id]?.selected;
        if (saved !== undefined) {
            return saved;
        }

        if (question.type === 'ordering') {
            return question.options.map((option) => option.id);
        }

        return null;
    }

    private getCurrentSelectedAnswer(): OptionId | OptionId[] | null {
        if (!this.currentQuestion) {
            return null;
        }

        if (this.currentQuestion.type === 'ordering') {
            if (Array.isArray(this.selectedOption)) {
                return [...this.selectedOption];
            }
            return this.currentQuestion.options.map((option) => option.id);
        }

        return this.selectedOption;
    }

    private isTrainingAnswerCorrect(selectedAnswer: OptionId | OptionId[], question: TrainingQuestion): boolean {
        const correctAnswer = question.correct_answer;
        if (correctAnswer === undefined || correctAnswer === null) {
            return false;
        }

        if (Array.isArray(correctAnswer)) {
            if (!Array.isArray(selectedAnswer) || selectedAnswer.length !== correctAnswer.length) {
                return false;
            }

            if (question.type === 'ordering') {
                return correctAnswer.every((answer, index) => this.isSameValue(answer, selectedAnswer[index]));
            }

            return correctAnswer.every((answer) =>
                selectedAnswer.some((selected) => this.isSameValue(answer, selected))
            );
        }

        return !Array.isArray(selectedAnswer) && this.isSameValue(selectedAnswer, correctAnswer);
    }

    private calculateTotalScore(): number {
        return Object.values(this.answers).reduce((sum, item) => sum + item.points, 0);
    }

    private getQuestionPoints(question: TrainingQuestion): number {
        const points = Number(question.points);
        return Number.isFinite(points) && points > 0 ? points : 10;
    }

    private isSameValue(firstValue: OptionId, secondValue: OptionId): boolean {
        return String(firstValue) === String(secondValue);
    }

    private getStoredNumber(key: string): number | null {
        try {
            const raw = localStorage.getItem(key);
            const n = Number(raw);
            return Number.isFinite(n) && n > 0 ? n : null;
        } catch {
            return null;
        }
    }

    private loadTrainingResults(): void {

        this.trainingService
            .getTrainingResults(
                this.childId,
                this.difficultyId
            )
            .pipe(takeUntil(this.destroy$))
            .subscribe({
                next: (res) => {

                    this.trainingResults =
                        (res?.data || [])
                            .sort((a: any, b: any) => a.level - b.level);

                    console.log(
                        'TRAINING RESULTS = ',
                        this.trainingResults
                    );
                }
            });
    }
}
