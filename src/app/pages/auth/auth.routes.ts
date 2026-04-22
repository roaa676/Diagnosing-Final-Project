import { Routes } from '@angular/router';
import { Login } from './Login/login';
import { RegisterComponent } from './Register/register';
import { TrainingComponent } from './training/training';
import { LearningDifficultiesComponent } from './learning_difficulties/learning_difficulties';


export default [
<<<<<<< HEAD
    { path: '', redirectTo: 'login', pathMatch: 'full' },
    { path: 'login', component: Login },
    { path: 'register', component: RegisterComponent },
    { path: '**', redirectTo: 'login' }
] as Routes;
=======
  { path: 'login', component: Login },
  { path: 'register', component: RegisterComponent },
  { path: 'training', component: TrainingComponent },
  { path: 'difficulties', component: LearningDifficultiesComponent }
] as Routes;
>>>>>>> a572bae0b1b52b73e0fc4d62001b713bcd4a4729
