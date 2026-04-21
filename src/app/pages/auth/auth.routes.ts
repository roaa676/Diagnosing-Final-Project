import { Routes } from '@angular/router';
import { Login } from './Login/login';
import { RegisterComponent } from './Register/register';
import { TrainingComponent } from './training/training';
import { LearningDifficultiesComponent } from './learning_difficulties/learning_difficulties';


export default [
  { path: 'login', component: Login },
  { path: 'register', component: RegisterComponent },
  { path: 'training', component: TrainingComponent },
  { path: 'difficulties', component: LearningDifficultiesComponent }
] as Routes;