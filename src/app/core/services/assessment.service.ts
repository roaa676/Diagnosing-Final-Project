import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface AssessmentQuestion {
    id: number;
    question: string;
    type: 'multiple_choice' | 'true_false' | 'matching' | 'ordering' | 'audio' | 'visual' | string;
    options: Array<string | AssessmentOption>;
    correct_answer?: number | string | Array<number | string>;
    time_limit?: number;
    explanation?: string;
    points?: number;
    category?: string;
    audio_url?: string;
    image_url?: string;
}

export interface AssessmentOption {
    id: number | string;
    text: string;
    image?: string;
}

export interface AssessmentLevel {
    difficulty_level: number;
    level_name: string;
    questions: AssessmentQuestion[];
}

export interface AssessmentResponse {
    status: string;
    message?: string;
    data?: AssessmentLevel[];
}


export interface QuestionnaireSubmission {
    child_id: number;
    learning_difficulty_id: number;
    [key: string]: any;
}

export interface AssessmentResultSubmission {
    child_id: number;
    game_type: string;
    raw_score: number;
    session_type?: 'assessment' | 'training';
    learning_difficulty_id?: number;
    difficulty_level?: any;
    correct_count?: number;
    total_questions?: number;
}

@Injectable({ providedIn: 'root' })
export class AssessmentService {
    private apiUrl = 'http://127.0.0.1:8000/api';

    constructor(private http: HttpClient) { }
    /**
     * 2. Assessment (اختبار الطفل) - /api/assessment-content/{id}
     */
    getAssessmentContent(difficultyId: number): Observable<AssessmentResponse> {
        return this.http.get<AssessmentResponse>(
            `${this.apiUrl}/assessment-content/${difficultyId}`
        );
    }

    submitAssessmentResult(data: AssessmentResultSubmission): Observable<any> {
        return this.http.post<any>(
            `${this.apiUrl}/submit-game-result`,
            data
        );
    }

    getChildResults(childId: number): Observable<any> {
        return this.http.get<any>(`${this.apiUrl}/results/${childId}`);
    }

    getAssessmentResult(childId: number, gameType: string): Observable<any> {
        return this.http.get<any>(
            `${this.apiUrl}/assessment-result/${childId}?game_type=${encodeURIComponent(gameType)}`,
        );
    }
}
