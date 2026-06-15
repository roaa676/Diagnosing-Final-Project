import { Routes } from '@angular/router';


export default [
    { path: '', redirectTo: '/dashboard', pathMatch: 'full' },
    { path: 'login', redirectTo: '/auth/login', pathMatch: 'full' },
    { path: 'register', redirectTo: '/auth/register', pathMatch: 'full' },
    { path: 'learning-difficulties', redirectTo: '/learning-difficulties', pathMatch: 'full' },
    { path: 'training', redirectTo: '/training', pathMatch: 'full' },
    { path: '**', redirectTo: '/dashboard' }
] as Routes;
