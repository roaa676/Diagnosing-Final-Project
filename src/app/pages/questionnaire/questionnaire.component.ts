import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { Subject } from 'rxjs';
import { takeUntil } from 'rxjs/operators';
import { QuestionnaireService } from '@/core/services/questionnaire.service';
import { ChildService } from '@/core/services/child.service';

interface QuestionnaireQuestion {
  id: number;
  text: string;
  answer?: number; 
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
  questions: QuestionnaireQuestion[] = [];

  answers: QuestionnaireData = {
    child_id: 0,
    learning_difficulty_id: 0,
    q1_reading_aloud: -1,
    q2_confusing_letters: -1,
    q3_forgetting_instructions: -1,
    q4_avoiding_reading: -1
  };
  loadingQuestions = false;
  questionsError = '';
  questionAnswers: Record<number, number> = {};
  loading = false;
  submitted = false;
  successMessage = false;
  errorMessage = '';
  currentChild: any = null;
  progressPercentage = 0;

  private destroy$ = new Subject<void>();

  constructor(
    private questionnaireService: QuestionnaireService,
    private childService: ChildService,
    private router: Router,
    private route: ActivatedRoute
  ) { }

  ngOnInit(): void {
    console.log('Questionnaire page loaded');
    this.loadChildData();
    this.loadQuestions();
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  loadQuestions(): void {
    this.loadingQuestions = true;

    const questionnaireId = 1;

    console.log('Calling questionnaire endpoint:', questionnaireId);

    this.questionnaireService
      .getQuestionnaire(questionnaireId)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (response: any) => {
          console.log('Questionnaire response:', response);

          this.questions = (response.data || []).map((q: any) => ({
            id: q.id,
            text: q.question_text
          }));

          this.loadingQuestions = false;
          this.updateProgress();
        },
        error: (error) => {
          console.error('Questionnaire error:', error);

          this.questionsError =
            error?.error?.message || 'ظپط´ظ„ طھط­ظ…ظٹظ„ ط£ط³ط¦ظ„ط© ط§ظ„ط§ط³طھط¨ظٹط§ظ†';

          this.loadingQuestions = false;
        }
      });
  }

  loadChildData(): void {
    const childId = this.route.snapshot.paramMap.get('childId');
    const difficultyId = this.getStoredDifficultyId();

    if (childId) {
      this.answers.child_id = parseInt(childId, 10);
    }

    if (difficultyId) {
      this.answers.learning_difficulty_id = difficultyId;
    }
  }

  selectAnswer(questionId: number, value: number): void {
    this.questionAnswers[questionId] = value;
    this.updateProgress();
  }

  updateProgress(): void {
    const answered = this.questions.filter(
      (q) => this.questionAnswers[q.id] !== undefined
    ).length;

    const total = this.questions.length;

    this.progressPercentage =
      total > 0 ? Math.round((answered / total) * 100) : 0;
  }

  getAnswerValue(questionId: number): number {
    return this.questionAnswers[questionId] ?? -1;
  }

  isAllAnswered(): boolean {
    return this.questions.length > 0 && this.questions.every((q) => this.questionAnswers[q.id] !== undefined);
  }

  
  submitAnswers(): void {
    console.log("SUBMIT CLICKED");
    console.log('answers object:', this.answers);
    console.log('child_id:', this.answers?.child_id);
    if (!this.isAllAnswered()) {
      this.errorMessage = 'ظٹط±ط¬ظ‰ ط§ظ„ط¥ط¬ط§ط¨ط© ط¹ظ„ظ‰ ط¬ظ…ظٹط¹ ط§ظ„ط£ط³ط¦ظ„ط©';
      return;
    }

    if (!this.answers.child_id) {
      this.errorMessage = 'ظ…ط¹ط±ظ‘ظپ ط§ظ„ط·ظپظ„ ط£ظˆ ظ…ط³طھظˆظ‰ ط§ظ„طµط¹ظˆط¨ط© ط؛ظٹط± طµط­ظٹط­. ظٹط±ط¬ظ‰ ط¥ط¹ط§ط¯ط© ط§ط®طھظٹط§ط± ط§ظ„ط·ظپظ„ ط£ظˆ ط§ظ„طھط£ظƒط¯ ظ…ظ† طھط³ط¬ظٹظ„ظ‡.';
      return;
    }
    console.log('ROUTE CHILD ID = ', this.route.snapshot.paramMap.get('childId'));
    console.log('ANSWERS CHILD ID = ', this.answers.child_id);
    const payload = {
      child_id: this.answers.child_id,
      learning_difficulty_id: this.answers.learning_difficulty_id,
      q1_reading_aloud: this.getAnswerForIndex(0),
      q2_confusing_letters: this.getAnswerForIndex(1),
      q3_forgetting_instructions: this.getAnswerForIndex(2),
      q4_avoiding_reading: this.getAnswerForIndex(3)
    };

    console.log('[Questionnaire Frontend Check] Payload before send:', payload);
   
    this.loading = true;
    this.errorMessage = '';
    console.log('ROUTE CHILD ID = ', this.route.snapshot.paramMap.get('childId'));
    console.log('ANSWERS CHILD ID = ', this.answers.child_id);
    console.log('PAYLOAD = ', payload);
    this.questionnaireService.submitQuestionnaire(payload)
      .pipe(takeUntil(this.destroy$))
      .subscribe({
        next: (response: any) => {
          this.loading = false;
          this.submitted = true;
          this.successMessage = true;
          localStorage.setItem('questionnaireResult', JSON.stringify(response));
        },
        error: (error: any) => {
          this.loading = false;
          console.error('Error Details:', error);
          this.errorMessage = error?.error?.message || 'ط­ط¯ط« ط®ط·ط£ ط£ط«ظ†ط§ط، ط¥ط±ط³ط§ظ„ ط§ظ„ط§ط³طھط¨ظٹط§ظ†';
        }
      });
  }

  private getAnswerForIndex(index: number): number {
    const question = this.questions[index];
    if (!question) {
      return -1;
    }
    return this.questionAnswers[question.id] ?? -1;
  }

  resetQuestionnaire(): void {
    this.questionAnswers = {};
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
        difficultyId: this.answers.learning_difficulty_id,
      }
    });
  }

  navigateToDashboard(): void {
    this.router.navigate(['/dashboard']);
  }


  private getStoredDifficultyId(): number {

    const rawValue =
      this.route.snapshot.queryParamMap.get('difficultyId') ??
      localStorage.getItem('selected_difficulty_id') ??
      localStorage.getItem('difficulty_id');

    const difficultyId = Number(rawValue);
    return Number.isFinite(difficultyId) && difficultyId > 0 ? difficultyId : 1;
  }
}


