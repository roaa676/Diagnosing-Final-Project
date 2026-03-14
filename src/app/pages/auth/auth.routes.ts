import { Routes } from '@angular/router';
import { Login } from './Login/login';
import { RegisterComponent } from './Register/register';
import { TrainingComponent } from './training/training';

export default [
    { path: 'login', component: Login },
    { path: 'register', component: RegisterComponent },
    { path: 'training', component: TrainingComponent }
] as Routes;