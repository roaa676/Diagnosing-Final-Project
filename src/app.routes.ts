import { Routes } from '@angular/router';
import { AppLayout } from './app/core/shared/layout/app-layout';
import { Documentation } from './app/pages/documentation/documentation';
import { Notfound } from './app/pages/notfound/notfound';
import { Dashboard } from '@/pages/Dashboard/dashboard';
import { Dashboardd } from '@/pages/dashboardd/dashboardd';


export const appRoutes: Routes = [
    {
        path: '',
        component: AppLayout,
        children: [
            { path: '', component: Dashboardd },
            { path: 'uikit', loadChildren: () => import('./app/pages/uikit/uikit.routes') },
            { path: 'documentation', component: Documentation },
            { path: 'pages', loadChildren: () => import('./app/pages/pages.routes') }
        ]
    },
    { path: 'dashboard', component: Dashboard },
    { path: 'notfound', component: Notfound },
    { path: 'auth', loadChildren: () => import('./app/pages/auth/auth.routes') },
    { path: '**', redirectTo: '/dashboard' }
];
