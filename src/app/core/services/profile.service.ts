import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface UserProfile {
  id: number;
  name: string;
  email: string;
  phone?: string;
  image_url?: string;
  created_at?: string;
}

export interface ProfileResponse {
  status: string;
  message: string;
  user?: UserProfile;
}

export interface PasswordChangeRequest {
  current_password: string;
  new_password: string;
  new_password_confirmation: string;
}

@Injectable({ providedIn: 'root' })
export class ProfileService {
  private apiUrl = 'http://127.0.0.1:8000/api';

  constructor(private http: HttpClient) {}

  /**
   * Get user profile
   */
  getProfile(): Observable<ProfileResponse> {
    return this.http.get<ProfileResponse>(`${this.apiUrl}/user/profile`);
  }

  /**
   * Update user profile
   */
  updateProfile(userData: Partial<UserProfile>): Observable<ProfileResponse> {
    return this.http.put<ProfileResponse>(`${this.apiUrl}/user/profile/update`, userData);
  }

  /**
   * Change password
   */
  changePassword(passwordData: PasswordChangeRequest): Observable<ProfileResponse> {
    return this.http.put<ProfileResponse>(`${this.apiUrl}/user/profile/password`, passwordData);
  }

  /**
   * Upload profile image
   */
  uploadProfileImage(file: File): Observable<ProfileResponse> {
    const formData = new FormData();
    formData.append('image', file);
    return this.http.post<ProfileResponse>(`${this.apiUrl}/user/upload-image`, formData);
  }
}
