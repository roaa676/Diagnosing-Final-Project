import { Routes } from '@angular/router';
import { Login } from './Login/login';
import { Register } from './Register/register';

export default [
    { path: 'login', component: Login },
    { path: 'register', component: Register }
] as Routes;
