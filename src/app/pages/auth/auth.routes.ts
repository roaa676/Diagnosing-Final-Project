import { Routes } from '@angular/router';
import { Login } from './Login/login';
import { RegisterComponent } from './Register/register';

export default [
    { path: 'login', component: Login },
    { path: 'register', component: RegisterComponent }
] as Routes;