import { Routes } from '@angular/router';
import { AppLayout } from './app/core/shared/layout/app-layout';
import { TrainingComponent } from '@/pages/Training/training';
import { TrainingLevelsComponent } from '@/pages/Training/Training-Levels/training-levels';
import { HistoryComponent } from './app/pages/history/history.component';
import { ProfileComponent } from '@/pages/profile/profile.component';
import { LearningDifficultiesComponent } from '@/pages/Learning-difficulties/Learning-difficulties';
import { QuestionnaireComponent } from '@/pages/questionnaire/questionnaire.component';
import { Dashboard } from '@/pages/Dashboard/dashboard';
import { AssessmentComponent } from '@/pages/assessment/assessment.component';
import { Login } from '@/pages/auth/Login/login';



export const appRoutes: Routes = [
    {
        path: '',
        component: AppLayout,
        children: [
            { path: '', component: Login },
            { path: 'pages', loadChildren: () => import('./app/pages/pages.routes') }
        ]
    },
    { path: 'dashboard', component: Dashboard },
    { path: 'login', redirectTo: 'auth/login', pathMatch: 'full' },
    { path: 'register', redirectTo: 'auth/register', pathMatch: 'full' },
    { path: 'auth', loadChildren: () => import('./app/pages/auth/auth.routes') },
    { path: 'learning-difficulties', component: LearningDifficultiesComponent },
    { path: 'questionnaire', component: QuestionnaireComponent },
    { path: 'assessment', component: AssessmentComponent },
    { path: 'training/levels', component: TrainingLevelsComponent },
    { path: 'training', component: TrainingComponent },
    { path: 'history', component: HistoryComponent },
    { path: 'profile', component: ProfileComponent },
    { path: '**', redirectTo: 'auth/login' }
];
