import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { Subject } from 'rxjs';
import { takeUntil } from 'rxjs/operators';
import { AssessmentService } from '@/core/services/assessment.service';
import { ChildService } from '@/core/services/child.service';

interface QuestionnaireQuestion {
  id: number;
  text: string;
  answer?: number; // 0=لا، 1=أحياناً، 2=نعم
}

interface QuestionnaireData {
  child_id: number;
  learning_difficulty_id: number;
  q1_reading_aloud: number;
  q2_confusing_letters: number;
  q3_forgetting_instructions: number;
  q4_avoiding_reading: number;
}

@Component({
  selector: 'app-questionnaire',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './questionnaire.component.html',
  styleUrls: ['./questionnaire.component.css']
})
export class QuestionnaireComponent implements OnInit, OnDestroy {
  questions: QuestionnaireQuestion[] = [
    {
      id: 1,
      text: 'هل يجد الطفل صعوبة في القراءة بصوت عالٍ؟'
    },
    {
      id: 2,
      text: 'هل يخلط الطفل بين الحروف المتشابهة (ب، د)؟'
    },
    {
      id: 3,
      text: 'هل ينسى الطفل التعليمات الموجهة إليه بسرعة؟'
    },
    {
      id: 4,
      text: 'هل يتجنب الطفل الأنشطة التي تتطلب القراءة؟'
    }
  ];

  answers: QuestionnaireData = {
    child_id: 0,
    learning_difficulty_id: 0,
    q1_reading_aloud: -1,
    q2_confusing_letters: -1,
    q3_forgetting_instructions: -1,
    q4_avoiding_reading: -1
  };

  loading = false;
  submitted = false;
  successMessage = false;
  errorMessage = '';
  currentChild: any = null;
  progressPercentage = 0;

  private destroy$ = new Subject<void>();

  constructor(
    private assessmentService: AssessmentService,
    private childService: ChildService,
    private router: Router,
    private route: ActivatedRoute
  ) {}

  ngOnInit(): void {
    this.loadChildData();
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  loadChildData(): void {
    const childId = this.route.snapshot.queryParamMap.get('childId');
    const difficultyId = this.route.snapshot.queryParamMap.get('difficultyId');

    if (childId) {
      this.answers.child_id = parseInt(childId, 10);
    }
    if (difficultyId) {
      this.answers.learning_difficulty_id = parseInt(difficultyId, 10);
    }

    // في الواقع، يمكن جلب بيانات الطفل الحالي إذا لزم الأمر
  }

  selectAnswer(questionId: number, value: number): void {
    const answerKey = `q${questionId}_${['reading_aloud', 'confusing_letters', 'forgetting_instructions', 'avoiding_reading'][questionId - 1]}`;
    
    switch(questionId) {
      case 1:
        this.answers.q1_reading_aloud = value;
        break;
      case 2:
        this.answers.q2_confusing_letters = value;
        break;
      case 3:
        this.answers.q3_forgetting_instructions = value;
        break;
      case 4:
        this.answers.q4_avoiding_reading = value;
        break;
    }

    this.updateProgress();
  }

  updateProgress(): void {
    const answered = [
      this.answers.q1_reading_aloud >= 0,
      this.answers.q2_confusing_letters >= 0,
      this.answers.q3_forgetting_instructions >= 0,
      this.answers.q4_avoiding_reading >= 0
    ].filter(v => v).length;

    this.progressPercentage = Math.round((answered / 4) * 100);
  }

  getAnswerValue(questionId: number): number {
    switch(questionId) {
      case 1:
        return this.answers.q1_reading_aloud;
      case 2:
        return this.answers.q2_confusing_letters;
      case 3:
        return this.answers.q3_forgetting_instructions;
      case 4:
        return this.answers.q4_avoiding_reading;
      default:
        return -1;
    }
  }

  isAllAnswered(): boolean {
    return (
      this.answers.q1_reading_aloud >= 0 &&
      this.answers.q2_confusing_letters >= 0 &&
      this.answers.q3_forgetting_instructions >= 0 &&
      this.answers.q4_avoiding_reading >= 0
    );
  }

  submitAnswers(): void {
    if (!this.isAllAnswered()) {
      this.errorMessage = 'يرجى الإجابة على جميع الأسئلة';
      return;
    }

    if (!this.answers.child_id || !this.answers.learning_difficulty_id) {
      this.errorMessage = 'معرّف الطفل أو مستوى الصعوبة غير صحيح';
      return;
    }

    this.loading = true;
    this.errorMessage = '';

    this.assessmentService.submitQuestionnaire(this.answers)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (response: any) => {
          this.loading = false;
          this.submitted = true;
          this.successMessage = true;
          // Store result for next page
          localStorage.setItem('questionnaireResult', JSON.stringify(response));
        },
        error: (error: any) => {
          this.loading = false;
          this.errorMessage = error?.error?.message || 'حدث خطأ أثناء إرسال الاستبيان';
        }
      });
  }

  resetQuestionnaire(): void {
    this.answers = {
      child_id: this.answers.child_id,
      learning_difficulty_id: this.answers.learning_difficulty_id,
      q1_reading_aloud: -1,
      q2_confusing_letters: -1,
      q3_forgetting_instructions: -1,
      q4_avoiding_reading: -1
    };
    this.errorMessage = '';
    this.submitted = false;
    this.successMessage = false;
    this.progressPercentage = 0;
  }

  navigateToAssessment(): void {
    this.router.navigate(['/assessment'], {
      queryParams: {
        childId: this.answers.child_id,
        difficultyId: this.answers.learning_difficulty_id
      }
    });
  }
}
