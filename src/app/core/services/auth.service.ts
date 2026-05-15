import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { BehaviorSubject, Observable } from 'rxjs';
import { tap } from 'rxjs/operators';

export interface LoginRequest {
  email: string;
  password: string;
}

export interface RegisterRequest {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
  children: { name: string; age: number }[];
}

export interface AuthResponse {
  token?: string;
  message?: string;
  user?: any;
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = 'http://127.0.0.1:8080/api';
  private isLoggedIn = new BehaviorSubject<boolean>(!!localStorage.getItem('auth_token'));
  public isLoggedIn$ = this.isLoggedIn.asObservable();

  constructor(private http: HttpClient) {}

  /**
   * User Login
   */
  login(email: string, password: string): Observable<AuthResponse> {
    const request: LoginRequest = { email, password };
    return this.http.post<AuthResponse>(`${this.apiUrl}/login`, request).pipe(
      tap((response) => {
        if (response.token) {
          localStorage.setItem('auth_token', response.token);
          this.isLoggedIn.next(true);
        }
      })
    );
  }

  /**
   * User Registration
   */
  register(name: string, email: string, password: string, confirmPassword: string, children: any[]): Observable<AuthResponse> {
    const request: RegisterRequest = {
      name,
      email,
      password,
      password_confirmation: confirmPassword,
      children
    };
    return this.http.post<AuthResponse>(`${this.apiUrl}/register`, request).pipe(
      tap((response) => {
        if (response.token) {
          localStorage.setItem('auth_token', response.token);
          this.isLoggedIn.next(true);
        }
      })
    );
  }

  /**
   * Get stored authentication token
   */
  getToken(): string | null {
    return localStorage.getItem('auth_token');
  }

  /**
   * Check if user is logged in
   */
  isAuthenticated(): boolean {
    return !!this.getToken();
  }

  /**
   * Logout user
   */
  logout(): void {
    localStorage.removeItem('auth_token');
    this.isLoggedIn.next(false);
  }

  /**
   * Get Bearer token for HTTP headers
   */
  getAuthHeaders(): HttpHeaders {
    const token = this.getToken();
    return new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    });
  }
}
