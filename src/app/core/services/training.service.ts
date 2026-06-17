import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface TrainingLevel {
  id: number;
  name: string;
  difficulty: number;
  description: string;
  completed: boolean;
}

export interface TrainingRoadmap {
  child_id: number;
  levels: TrainingLevel[];
  progress: number;
}

export interface GameContent {
  id: number;
  type: string;
  title: string;
  description: string;
  content: any;
  difficulty_level: number;
}

export interface TrainingResponse {
  status: string;
  message?: string;
  data?: any;
}

@Injectable({ providedIn: 'root' })
export class TrainingService {
  private apiUrl = 'http://127.0.0.1:8000/api';

  constructor(private http: HttpClient) { }


  getTrainingRoadmap(childId: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/training/roadmap/${childId}`);
  }


  getGameContent(difficultyId: number, level: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/game-content/${difficultyId}/${level}`);
  }


  completeTrainingLevel(childId: number, trainingType: string): Observable<TrainingResponse> {
    return this.http.post<TrainingResponse>(`${this.apiUrl}/training/complete`, {
      child_id: childId,
      training_type: trainingType
    });
  }
  getTrainingResults(
    childId: number,
    difficultyId: number
  ): Observable<any> {

    return this.http.get(
      `${this.apiUrl}/training/results/${childId}?difficulty_id=${difficultyId}`
    );
  }

  submitGameResult(payload: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/submit-game-result`, payload);
  }
}
