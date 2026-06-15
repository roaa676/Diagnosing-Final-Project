import { CommonModule } from '@angular/common';
import { Component, OnDestroy, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Subject, takeUntil } from 'rxjs';
import { TrainingService } from '@/core/services/training.service';

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
    // منطق التحكم في الصوت
    playingAudio = false;
    private currentAudio: HTMLAudioElement | null = null;

    private destroy$ = new Subject<void>();

    loading = true;
    errorMessage = '';

    questions: TrainingQuestion[] = [];
    currentQuestionIndex = 0;
    selectedOption: OptionId | OptionId[] | null = null;

    difficultyId!: number;
    level!: number;
    childId!: number;

    constructor(
        private readonly route: ActivatedRoute,
        private readonly router: Router,
        private readonly trainingService: TrainingService
    ) {}

    ngOnInit(): void {
        const difficultyIdRaw = this.route.snapshot.queryParamMap.get('difficultyId');
        const levelRaw = this.route.snapshot.queryParamMap.get('level');
        const childIdRaw = this.route.snapshot.queryParamMap.get('childId') || this.getStoredNumber('child_id');

        const difficultyId = Number(difficultyIdRaw);
        const level = Number(levelRaw);
        const childId = Number(childIdRaw);

        if (!Number.isFinite(difficultyId) || difficultyId <= 0 || !Number.isFinite(level) || level <= 0 || !Number.isFinite(childId) || childId <= 0) {
            this.errorMessage = 'معرّفات التدريب غير صحيحة';
            this.loading = false;
            return;
        }

        this.difficultyId = difficultyId;
        this.level = level;
        this.childId = childId;

        this.fetchQuestions();
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

    private fetchQuestions(): void {
        this.trainingService
            .getGameContent(this.difficultyId, this.level)
            .pipe(takeUntil(this.destroy$))
            .subscribe({
                next: (res: TrainingLevelResponse) => {
                    const dataQuestions = (res?.data?.questions ?? res?.questions ?? []) as TrainingQuestion[];
                    this.questions = dataQuestions;

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
        // MVP: لا نحسب score حالياً، فقط تنقل داخل الصفحة.
        if (this.isLastQuestion) {
            this.submitAndExit();
            return;
        }

        this.currentQuestionIndex += 1;
        this.selectedOption = null;
    }

    private submitAndExit(): void {
        // MVP بسيط: score افتراضي 0 لتجنب أخطاء بدون منطق تصحيح كامل.
        this.trainingService
            .submitGameResult(this.childId, String(this.difficultyId), 0)
            .pipe(takeUntil(this.destroy$))
            .subscribe({
                next: () => {
                    this.router.navigate(['/training/levels'], {
                        queryParams: { childId: this.childId, difficultyId: this.difficultyId }
                    });
                },
                error: () => {
                    // حتى لو فشل submission، ارجع للمستويات
                    this.router.navigate(['/training/levels'], {
                        queryParams: { childId: this.childId, difficultyId: this.difficultyId }
                    });
                }
            });
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
}
