import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Child {
  id: number;
  name: string;
  age: number;
  image?: string;
  created_at?: string;
}

export interface ChildrenResponse {
  status: string;
  message?: string;
  data: Child[];
}

export interface ChildResponse {
  status: string;
  message?: string;
  data?: Child[];
}

@Injectable({ providedIn: 'root' })
export class ChildService {
  private apiUrl = 'http://127.0.0.1:8000/api';

  constructor(private http: HttpClient) { }


  getAllChildren(): Observable<ChildrenResponse> {
    return this.http.get<ChildrenResponse>(
      `${this.apiUrl}/children`
    );
  }

  createChild(name: string, age: number): Observable<ChildResponse> {
    return this.http.post<ChildResponse>(
      `${this.apiUrl}/children`,
      { name, age }
    );
  }

  uploadChildImage(
    childId: number,
    file: File
  ): Observable<any> {

    const formData = new FormData();
    formData.append('image', file);

    return this.http.post(
      `${this.apiUrl}/child/${childId}/upload-image`,
      formData
    );
  }
}