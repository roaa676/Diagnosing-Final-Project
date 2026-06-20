import { TrainingService } from '@/core/services/training.service';
import { CommonModule } from '@angular/common';
import { Component, OnDestroy, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { Subject } from 'rxjs';
import { DialogModule } from "primeng/dialog";

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
    imports: [CommonModule, DialogModule],
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
        this.loadRoadmap();
    }

    ngOnDestroy(): void {
        this.destroy$.next();
        this.destroy$.complete();
    }

    startLevel(level: LevelCard): void {

        if (level.buttonVariant === 'locked') {
            return;
        }

        this.router.navigate(['/training/game'], {
            queryParams: {
                childId: this.childId,
                difficultyId: this.difficultyId,
                level: level.levelNumber
            }
        });
    }

    private loadRoadmap(): void {

        if (!this.childId || !this.difficultyId) {
            return;
        }

        this.trainingService
            .getTrainingRoadmap(
                this.childId,
                this.difficultyId
            )
            .subscribe({

                next: (res) => {

                    const roadmap = res.data?.[0];

                    if (!roadmap) {
                        return;
                    }

                    const currentLevel =
                        Number(roadmap.current_level);

                    const locked =
                        roadmap.is_locked;

                    this.levels = this.levels.map(level => {

                        if (level.levelNumber < currentLevel) {

                            return {
                                ...level,
                                buttonLabel: 'تم اجتيازه',
                                buttonIcon: 'pi pi-check',
                                buttonVariant: 'outline',
                                disabled: false
                            };
                        }

                        if (level.levelNumber === currentLevel) {

                            return {
                                ...level,
                                buttonLabel: locked
                                    ? 'يفتح خلال 24 ساعة'
                                    : 'ابدأ التدريب',
                                buttonIcon: locked
                                    ? 'pi pi-clock'
                                    : 'pi pi-play',
                                buttonVariant: locked
                                    ? 'locked'
                                    : 'solid',
                                disabled: locked,
                            };
                        }

                        return {
                            ...level,
                            buttonLabel: 'مقفل حالياً',
                            buttonIcon: 'pi pi-lock',
                            buttonVariant: 'locked',
                            disabled: true
                        };
                    });
                }
            });
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
