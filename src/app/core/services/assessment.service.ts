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
    // backend يرجع array مستويات في التقييم المبدئي
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
}

@Injectable({ providedIn: 'root' })
export class AssessmentService {
    private apiUrl = 'http://127.0.0.1:8000/api';

    constructor(private http: HttpClient) { }

    /**
     * Get assessment content for a specific learning difficulty
     */
    getAssessmentContent(difficultyId: number): Observable<AssessmentResponse> {
        // 1. جيب التوكن اللي اتخزن وقت تسجيل الدخول
        const token = localStorage.getItem('token');

        // 2. حط التوكن في الـ Headers
        const headers: any = {
            'Accept': 'application/json'
        };

        // التحقق من أن التوكن موجود فعلياً وليس قيمة نصية فارغة أو "null"
        if (token && token !== 'null') {
            headers['Authorization'] = `Bearer ${token}`;
        }

        // 3. ابعت الطلب بالـ Headers الجديدة
        return this.http.get<AssessmentResponse>(
            `${this.apiUrl}/assessment-content/${difficultyId}`,
            { headers }
        );
    }
    /**
     * Submit questionnaire answers
     */
    submitQuestionnaire(data: QuestionnaireSubmission): Observable<AssessmentResponse> {
        return this.http.post<AssessmentResponse>(`${this.apiUrl}/submit-questionnaire`, data);
    }

    /**
     * Submit child assessment score
     */
    submitAssessmentResult(data: AssessmentResultSubmission): Observable<any> {
        const token = localStorage.getItem('token');

        const headers: any = {
            'Accept': 'application/json'
        };

        if (token && token !== 'null') {
            headers['Authorization'] = `Bearer ${token}`;
        }

        return this.http.post<any>(`${this.apiUrl}/submit-game-result`, data, { headers });
    }
    /**
     * Get assessment results for a child
     */
    getChildResults(childId: number): Observable<any> {
        return this.http.get<any>(`${this.apiUrl}/results/${childId}`);
    }

    /**
     * Get last assessment result for a child by game_type (difficultyId)
     */
    getAssessmentResult(childId: number, gameType: string): Observable<any> {
        return this.http.get<any>(`${this.apiUrl}/assessment-result/${childId}?game_type=${encodeURIComponent(gameType)}`);
    }
}


