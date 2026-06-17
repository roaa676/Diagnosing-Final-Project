import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';

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
    childId: number | null = null;
    difficultyId: number | null = null;

    trainings: TrainingCard[] = [
        {
            id: '1',
            title: 'القراءة',
            description:
                'مجموعة من التدريبات المصممة لمعالجة التحديات الأولية في التعلم. تركز هذه الأنشطة على تحسين مهارة القراءة وبناء الثقة بالنفس لدى الطفل.',
            image: 'assets/images/boy.png',
            theme: 'warm'
        },
        {
            id: '2',
            title: 'الحساب',
            description:
                'تمارين مخصصة لتعزيز الذاكرة العاملة وزيادة معدلات التركيز والانتباه. يساعد هذا القسم في الربط بين المعلومات البصرية و الحسابية مما يسهل استنتاج المعلومات الجديدة.',
            image: 'assets/images/Girl.png',
            theme: 'mint'
        },
    ];

    constructor(private readonly router: Router,
        private readonly route: ActivatedRoute
    ) { 
        this.childId = Number(this.route.snapshot.queryParamMap.get('childId'));
        this.difficultyId = Number(this.route.snapshot.queryParamMap.get('difficultyId'));
    }

    openTraining(training: TrainingCard): void {
        this.router.navigate(['/training/levels'], {
            queryParams: {
                childId: this.childId,
                difficultyId: training.id
            }
        });
    }
}
