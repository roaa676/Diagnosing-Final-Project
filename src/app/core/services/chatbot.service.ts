import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface ChatMessage {
  role: 'user' | 'assistant';
  text: string;
  timestamp: Date;
}

export interface AskResponse {
  status: string;
  answer: string;
}

export interface ExerciseResponse {
  status: string;
  answer: string;
  recommended_exercises: any[];
}

@Injectable({ providedIn: 'root' })
export class ChatbotService {
  private apiUrl = 'http://127.0.0.1:8080/api';

  constructor(private http: HttpClient) {}

  ask(message: string, childId?: number): Observable<AskResponse> {
    const body: any = { message };
    if (childId) body['child_id'] = childId;
    return this.http.post<AskResponse>(`${this.apiUrl}/chatbot/ask`, body);
  }

  explainResult(childId: number, resultId?: number): Observable<AskResponse> {
    const body: any = { child_id: childId };
    if (resultId) body['result_id'] = resultId;
    return this.http.post<AskResponse>(`${this.apiUrl}/chatbot/explain-result`, body);
  }

  recommendExercises(childId: number): Observable<ExerciseResponse> {
    return this.http.post<ExerciseResponse>(`${this.apiUrl}/chatbot/recommend-exercises`, { child_id: childId });
  }
}
