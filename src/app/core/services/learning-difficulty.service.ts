import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface LearningDifficulty {
  id: number;
  name: string;
  description: string;
  icon?: string;
  category?: string;
}

export interface Question {
  id: number;
  text: string;
  options: string[];
  answer: string;
  difficulty_id: number;
}

export interface DifficultyResponse {
  status: string;
  data: LearningDifficulty[];
}

export interface QuestionsResponse {
  status: string;
  data: Question[];
}

@Injectable({ providedIn: 'root' })
export class LearningDifficultyService {
  private apiUrl = 'http://127.0.0.1:8000/api';

  constructor(private http: HttpClient) {}

  /**
   * Get all learning difficulties
   */
  getAllDifficulties(): Observable<DifficultyResponse> {
    return this.http.get<DifficultyResponse>(`${this.apiUrl}/difficulties`);
  }

  /**
   * Get questions for a specific learning difficulty
   */
  getDifficultyQuestions(difficultyId: number): Observable<QuestionsResponse> {
    return this.http.get<QuestionsResponse>(`${this.apiUrl}/difficulties/${difficultyId}/questions`);
  }
}
