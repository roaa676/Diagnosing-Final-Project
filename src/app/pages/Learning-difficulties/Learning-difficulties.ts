import { CommonModule, Location } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { Router, RouterModule } from '@angular/router';
import { MenuItem } from 'primeng/api';
import { ButtonModule } from 'primeng/button';
import { MenuModule } from 'primeng/menu';
import { RippleModule } from 'primeng/ripple';

type DifficultyTheme = 'reading' | 'math';

interface DifficultyCard {
    title: string;
    latin: string;
    icon: string;
    theme: DifficultyTheme;
    imagePath: string;
    description: string;
    symptoms: string[];
    trainings: string[];
    guide: string;
}

@Component({
    selector: 'app-learning-difficulties',
    standalone: true,
    imports: [CommonModule, RouterModule, ButtonModule, MenuModule, RippleModule],
    templateUrl: './Learning-difficulties.html',
    styleUrls: ['./Learning-difficulties.css']})
export class LearningDifficultiesComponent implements OnInit {
    items: MenuItem[] = [];

    readonly difficulties: DifficultyCard[] = [
        {
            title: 'عسر القراءة',
            latin: 'Dyslexia',
            icon: 'fa-book-open',
            theme: 'reading',
            imagePath: 'assets/images/reading.png',
            description: 'صعوبة تعليمية محدودة تؤثر بشكل أساسي على المهارات المرتبطة بالقراءة والكتابة والتهجئة.',
            symptoms: [
                'صعوبة في تكوين جملة واضحة للقراءة.',
                'عدم القدرة على تمييز الحروف المتشابهة (ب/ت/ث).',
                'صعوبة في تكرار أسماء الحروف.'
            ],
            trainings: ['ألعاب صيد الحروف', 'القصص المسموعة', 'عرض كل الحروف'],
            guide: 'تعزيز التفاعل بين الحروف والكلمات، واستخدام الأساليب الحديثة لتجاوز العقبات.'
        },
        {
            title: 'عسر الحساب',
            latin: 'Dyscalculia',
            icon: 'fa-calculator',
            theme: 'math',
            imagePath: 'assets/images/math.png',
            description: 'صعوبة تعليمية تؤثر بشكل أساسي على المهارات الحسابية والرياضية مثل الفهم العددي والعمليات الحسابية.',
            symptoms: [
                'صعوبة في فهم الأرقام ومعانيها.',
                'صعوبة في إجراء العمليات الحسابية البسيطة.',
                'صعوبة في تقدير المسافات والكميات.'
            ],
            trainings: ['السبورة المغناطيسية', 'ألعاب الأرقام', 'عرض كل التمارين'],
            guide: 'استخدام أسلوب التعلم الحسابي المبني على التجربة واللعب، ودمج الأنشطة اليومية في التعلم.'
        }
    ];

    constructor(
        private readonly location: Location,
        public router: Router
    ) {}

    ngOnInit(): void {
        this.items = [
            {
                label: 'إنشاء حساب',
                icon: 'pi pi-user-plus',
                command: () => {
                    this.router.navigate(['/auth/register']);
                }
            },
            {
                label: 'تسجيل دخول',
                icon: 'pi pi-sign-in',
                command: () => {
                    this.router.navigate(['/auth/login']);
                }
            }
        ];
    }

    goBack(): void {
        if (typeof window !== 'undefined' && window.history.length > 1) {
            this.location.back();
            return;
        }

        this.router.navigate(['/dashboard']);
    }
}
