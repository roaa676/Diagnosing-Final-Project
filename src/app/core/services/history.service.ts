import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface HistoryEntry {
  id: number;
  activity_type: string;
  description: string;
  timestamp: string;
  result?: any;
}

export interface HistoryResponse {
  status: string;
  data: HistoryEntry[];
}

@Injectable({ providedIn: 'root' })
export class HistoryService {
  private apiUrl = 'http://127.0.0.1:8000/api';

  constructor(private http: HttpClient) {}

  /**
   * Get child activity history
   */
  getChildHistory(childId: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/child/${childId}/history`);
  }

  /**
   * Get comprehensive report for a child
   */
  getChildReport(childId: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/child/${childId}/report`);
  }
}
