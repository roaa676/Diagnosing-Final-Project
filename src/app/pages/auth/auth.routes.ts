import { Routes } from '@angular/router';
import { Login } from './Login/login';
import { RegisterComponent } from './Register/register';

// auth.routes.ts
export const AuthRoutes: Routes = [
  { path: '', component: LoginComponent },
  { path: 'register', component: RegisterComponent },
];