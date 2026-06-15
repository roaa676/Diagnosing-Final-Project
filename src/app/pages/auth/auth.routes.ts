import { Routes } from '@angular/router';
import { Login } from './Login/login';
import { RegisterComponent } from './Register/register';



export default [
    { path: '', redirectTo: 'login', pathMatch: 'full' },
    { path: 'login', component: Login },
    { path: 'register', component: RegisterComponent },
    { path: '**', redirectTo: 'login' }
] as Routes;

