import { Routes } from '@angular/router';
import { AppLayout } from './app/core/shared/layout/app-layout';
import { Dashboard } from '@/pages/Dashboard/dashboard';
import { LearningDifficultiesComponent } from './app/pages/Learning-difficulties/Learning-difficulties';
import { TrainingComponent } from '@/pages/Training/training';
import { TrainingLevelsComponent } from '@/pages/Training/Training-Levels/training-levels';



export const appRoutes: Routes = [
    {
        path: '',
        component: AppLayout,
        children: [
            { path: '', component: Dashboard },
            { path: 'pages', loadChildren: () => import('./app/pages/pages.routes') }
        ]
    },
    { path: 'dashboard', component: Dashboard },
    { path: 'login', redirectTo: 'auth/login', pathMatch: 'full' },
    { path: 'register', redirectTo: 'auth/register', pathMatch: 'full' },
    { path: 'auth', loadChildren: () => import('./app/pages/auth/auth.routes') },
    { path: 'learning-difficulties', component: LearningDifficultiesComponent },
    { path: 'training/levels', component: TrainingLevelsComponent },
    { path: 'training', component: TrainingComponent },
    { path: '**', redirectTo: '/dashboard' }
];
