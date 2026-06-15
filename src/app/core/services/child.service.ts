import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Child {
  id: number;
  name: string;
  age: number;
  created_at?: string;
}

export interface ChildResponse {
  status: string;
  message: string;
  child?: Child;
  children?: Child[];
}

@Injectable({ providedIn: 'root' })
export class ChildService {
  private apiUrl = 'http://127.0.0.1:8000/api';

  constructor(private http: HttpClient) {}

  /**
   * Get all children for the logged-in user
   */
  getAllChildren(): Observable<ChildResponse> {
    return this.http.get<ChildResponse>(`${this.apiUrl}/children`);
  }

  /**
   * Create a new child
   */
  createChild(name: string, age: number): Observable<ChildResponse> {
    return this.http.post<ChildResponse>(`${this.apiUrl}/children`, { name, age });
  }

  /**
   * Upload child profile image
   */
  uploadChildImage(childId: number, file: File): Observable<ChildResponse> {
    const formData = new FormData();
    formData.append('image', file);
    return this.http.post<ChildResponse>(`${this.apiUrl}/child/${childId}/upload-image`, formData);
  }
}
