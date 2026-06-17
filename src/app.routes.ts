import { Routes } from '@angular/router';
import { AppLayout } from './app/core/shared/layout/app-layout';
import { TrainingComponent } from '@/pages/Training/training';
import { TrainingLevelsComponent } from '@/pages/Training/Training-Levels/training-levels';
import { TrainingGameComponent } from '@/pages/Training/training-game/training-game.component';
import { HistoryComponent } from './app/pages/history/history.component';
import { ProfileComponent } from '@/pages/profile/profile.component';
import { LearningDifficultiesComponent } from '@/pages/Learning-difficulties/Learning-difficulties';
import { QuestionnaireComponent } from '@/pages/questionnaire/questionnaire.component';
import { AssessmentComponent } from '@/pages/assessment/assessment.component';
import { Login } from '@/pages/auth/Login/login';
import { Dashboard } from '@/pages/dashboard/dashboard';

export const appRoutes: Routes = [
    { path: '', redirectTo: 'login', pathMatch: 'full' },

    { path: 'login', component: Login },
    { path: 'register', redirectTo: 'auth/register', pathMatch: 'full' },
    { path: 'auth', loadChildren: () => import('./app/pages/auth/auth.routes') },

    {
        path: '',
        component: AppLayout,
        children: [
            { path: 'dashboard', component: Dashboard },
            { path: 'learning-difficulties', component: LearningDifficultiesComponent },
            { path: 'questionnaire/:childId', component: QuestionnaireComponent },
            { path: 'assessment', component: AssessmentComponent },
            { path: 'training/levels', component: TrainingLevelsComponent },
            { path: 'training/game', component: TrainingGameComponent },
            { path: 'training', component: TrainingComponent },
            { path: 'history', component: HistoryComponent },
            { path: 'profile', component: ProfileComponent },
        ]
    },

    { path: '**', redirectTo: 'login' }
];