import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';

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
}

@Component({
    selector: 'app-training-levels',
    standalone: true,
    imports: [CommonModule],
    templateUrl: './training-levels.html',
    styleUrls: ['./training-levels.css']
})
export class TrainingLevelsComponent {
    levels: LevelCard[] = [
        {
            title: 'المستوى الأول',
            description: 'تمارين تمهيدية بسيطة للتعرف على الأساسيات وبناء الثقة.',
            badge: 'البداية',
            visualTheme: 'green',
            buttonLabel: 'ابدأ التدريب',
            buttonIcon: 'pi pi-play',
            buttonVariant: 'solid'
        },
        {
            title: 'المستوى الثاني',
            description: 'تمارين متوسطة الصعوبة لتعزيز المهارات المكتسبة وتطبيقها.',
            visualTheme: 'blue',
            buttonLabel: 'ابدأ التدريب',
            buttonIcon: 'pi pi-play',
            buttonVariant: 'outline'
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
            disabled: true
        }
    ];

    startLevel(level: LevelCard): void {
        if (level.disabled) {
            return;
        }

        console.log('Start training level:', level.title);
    }
}
