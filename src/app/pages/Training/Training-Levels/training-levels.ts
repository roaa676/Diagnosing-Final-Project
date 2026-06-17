import { TrainingService } from '@/core/services/training.service';
import { CommonModule } from '@angular/common';
import { Component, OnDestroy, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { Subject, takeUntil } from 'rxjs';

interface LevelCard {
    title: string;
    description: string;
    badge?: string;
    badgeIcon?: string;
    visualTheme: 'green' | 'blue' | 'amber';
    buttonLabel: string;
    buttonIcon: string;
    buttonVariant: 'solid' | 'outline' | 'locked';
    disabled?: boolean;
    levelNumber: 1 | 2 | 3;
    score?: number;
}

@Component({
    selector: 'app-training-levels',
    standalone: true,
    imports: [CommonModule],
    templateUrl: './training-levels.html',
    styleUrls: ['./training-levels.css']
})
export class TrainingLevelsComponent implements OnInit, OnDestroy {
    levels: LevelCard[] = [
        {
            title: 'المستوى الأول',
            description: 'تمارين تمهيدية بسيطة للتعرف على الأساسيات وبناء الثقة.',
            badge: 'البداية',
            visualTheme: 'green',
            buttonLabel: 'ابدأ التدريب',
            buttonIcon: 'pi pi-play',
            buttonVariant: 'solid',
            disabled: false,
            levelNumber: 1
        },
        {
            title: 'المستوى الثاني',
            description: 'تمارين متوسطة الصعوبة لتعزيز المهارات المكتسبة وتطبيقها.',
            badge: 'متوسط',
            visualTheme: 'blue',
            buttonLabel: 'ابدأ التدريب',
            buttonIcon: 'pi pi-play',
            buttonVariant: 'outline',
            disabled: true,
            levelNumber: 2
        },
        {
            title: 'المستوى الثالث',
            description: 'تمارين متقدمة ومكثفة لتثبيت المعلومات وتحدي القدرات.',
            badge: 'متقدم',
            badgeIcon: 'pi pi-lock',
            visualTheme: 'amber',
            buttonLabel: 'مقفل حالياً',
            buttonIcon: 'pi pi-lock',
            buttonVariant: 'locked',
            disabled: true,
            levelNumber: 3
        }
    ];

    private destroy$ = new Subject<void>();

    private childId: number | null = null;
    private difficultyId: number | null = null;

    constructor(
        private readonly router: Router,
        private readonly route: ActivatedRoute,
        private readonly trainingService: TrainingService
    ) { }

    ngOnInit(): void {
        this.childId = this.getNumericQueryParam('childId') ?? this.getLocalStorageNumber('child_id');
        this.difficultyId = this.getNumericQueryParam('difficultyId') ?? this.getLocalStorageNumber('difficulty_id');
        this.loadCompletedLevels();
        
        // this.applyUnlockRulesFromLocalStorage();
    }

    ngOnDestroy(): void {
        this.destroy$.next();
        this.destroy$.complete();
    }

    startLevel(level: LevelCard): void {
        if (level.disabled) {
            return;
        }
        console.log('CHILD ID =', this.childId);
        console.log('DIFFICULTY ID =', this.difficultyId);
        console.log('LEVEL =', level.levelNumber);
        this.router.navigate(['/training/game'], {
            queryParams: {
                childId: this.childId,
                difficultyId: this.difficultyId,
                level: level.levelNumber
            }
        });

    }
    private loadCompletedLevels(): void {

        if (!this.childId || !this.difficultyId) {
            return;
        }

        this.trainingService
            .getTrainingResults(this.childId, this.difficultyId)
            .subscribe({
                next: (res) => {

                    const completedLevels = res?.data || [];

                    const completedLevelNumbers =
                        completedLevels.map((x: any) => Number(x.level));

                    const highestCompleted =
                        completedLevelNumbers.length
                            ? Math.max(...completedLevelNumbers)
                            : 0;

                    const nextAvailableLevel = highestCompleted + 1;

                    this.levels = this.levels.map(level => {

                        if (completedLevelNumbers.includes(level.levelNumber)) {

                            return {
                                ...level,
                                buttonLabel: 'تم اجتيازه',
                                buttonIcon: 'pi pi-check',
                                buttonVariant: 'outline',
                                disabled: false,
                                badgeIcon: undefined
                            };
                        }

                        if (level.levelNumber === nextAvailableLevel) {

                            return {
                                ...level,
                                buttonLabel: 'ابدأ التدريب',
                                buttonIcon: 'pi pi-play',
                                buttonVariant: 'solid',
                                disabled: false,
                                badgeIcon: undefined
                            };
                        }

                        return {
                            ...level,
                            buttonLabel: 'مقفل حالياً',
                            buttonIcon: 'pi pi-lock',
                            buttonVariant: 'locked',
                            disabled: true,
                            badgeIcon: 'pi pi-lock'
                        };
                    });
                }
            });
    }

    private applyUnlockRulesFromLocalStorage(): void {
      
        const raw = localStorage.getItem('trainingProgress');
        if (!raw) {
            return; 
        }

        try {
            const progress = JSON.parse(raw) as any;
            const currentLevel = Number(progress?.current_level);
            const progressPercentage = Number(progress?.progress_percentage);

            this.levels = this.levels.map((lvl) => {
                const unlocked = currentLevel >= lvl.levelNumber || (lvl.levelNumber === 2 && progressPercentage >= 30);
                if (unlocked) {
                    return {
                        ...lvl,
                        disabled: false,
                        buttonLabel: lvl.levelNumber === 3 ? 'ابدأ التدريب' : lvl.buttonLabel,
                        buttonVariant: lvl.levelNumber === 1 ? 'solid' : (lvl.levelNumber === 2 ? 'outline' : 'solid'),
                        buttonIcon: 'pi pi-play',
                        badgeIcon: lvl.levelNumber === 3 ? undefined : lvl.badgeIcon
                    };
                }

                return {
                    ...lvl,
                    disabled: true,
                    buttonLabel: 'مقفل حالياً',
                    buttonVariant: 'locked',
                    buttonIcon: 'pi pi-lock',
                    badgeIcon: 'pi pi-lock'
                };
            });
        } catch {
            
        }
    }

    private getNumericQueryParam(paramName: string): number | null {
        const value = Number(this.route.snapshot.queryParamMap.get(paramName));
        return Number.isFinite(value) && value > 0 ? value : null;
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
    
    goBack(): void {
        this.router.navigate(['/training']);
    }
}
