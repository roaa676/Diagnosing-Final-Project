import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { Router } from '@angular/router';

interface TrainingCard {
    id: string;
    title: string;
    description: string;
    image: string;
    theme: 'warm' | 'mint' | 'sky';
}

@Component({
    selector: 'app-training',
    standalone: true,
    imports: [CommonModule],
    templateUrl: './training.html',
    styleUrls: ['./training.css']
})
export class TrainingComponent {
    trainings: TrainingCard[] = [
        {
            id: '1',
            title: 'صعوبة 1',
            description:
                'مجموعة من التدريبات المصممة لمعالجة التحديات الأولية في التعلم. تركز هذه الأنشطة على تحسين المهارات الحسية والحركية الأساسية وبناء الثقة بالنفس لدى الطفل.',
            image: 'assets/images/boy.png',
            theme: 'warm'
        },
        {
            id: '2',
            title: 'صعوبة 2',
            description:
                'تمارين مخصصة لتعزيز الذاكرة العاملة وزيادة معدلات التركيز والانتباه. يساعد هذا القسم في الربط بين المعلومات البصرية والسمعية مما يسهل استيعاب المعلومات الجديدة.',
            image: 'assets/images/Girl.png',
            theme: 'mint'
        },
        {
            id: '3',
            title: 'صعوبة 3',
            description:
                'أنشطة وتدريبات متقدمة تستهدف تطوير الفهم القرائي والعمليات الحسابية والمنطقية. يوفر هذا القسم استراتيجيات تعليمية للتعامل مع المفاهيم المعقدة ودعم الطفل أكاديميًا.',
            image: 'assets/images/write-IMG.png',
            theme: 'sky'
        }
    ];

    constructor(private readonly router: Router) { }

    openTraining(training: TrainingCard): void {
        this.router.navigate(['/training/levels'], {
            state: { trainingId: training.id }
        });
    }
}
