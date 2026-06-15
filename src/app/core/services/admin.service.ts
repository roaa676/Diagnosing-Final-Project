import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface AdminStats {
  total_users: number;
  total_children: number;
  total_assessments: number;
  avg_score: number;
}

export interface AdminQuestion {
  id: number;
  text: string;
  difficulty_id: number;
  category: string;
}

export interface AdminResponse {
  status: string;
  message?: string;
  data?: any;
}

@Injectable({ providedIn: 'root' })
export class AdminService {
  private apiUrl = 'http://127.0.0.1:8000/api';

  constructor(private http: HttpClient) {}

  /**
   * Get admin statistics
   */
  getStats(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/admin/stats`);
  }

  /**
   * Get all questions (admin)
   */
  getAllQuestions(): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/admin/questions`);
  }

  /**
   * Create a new question
   */
  createQuestion(questionData: AdminQuestion): Observable<AdminResponse> {
    return this.http.post<AdminResponse>(`${this.apiUrl}/admin/questions`, questionData);
  }

  /**
   * Update a question
   */
  updateQuestion(questionId: number, questionData: AdminQuestion): Observable<AdminResponse> {
    return this.http.put<AdminResponse>(`${this.apiUrl}/admin/questions/${questionId}`, questionData);
  }

  /**
   * Delete a question
   */
  deleteQuestion(questionId: number): Observable<AdminResponse> {
    return this.http.delete<AdminResponse>(`${this.apiUrl}/admin/questions/${questionId}`);
  }
}
