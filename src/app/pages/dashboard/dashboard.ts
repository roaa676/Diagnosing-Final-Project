import { Component } from '@angular/core';
import { Router, RouterModule } from '@angular/router';
import { RippleModule } from 'primeng/ripple';
import { StyleClassModule } from 'primeng/styleclass';
import { ButtonModule } from 'primeng/button';
import { DividerModule } from 'primeng/divider';
import { MenuItem } from 'primeng/api';
import { MenuModule } from 'primeng/menu';

@Component({
    selector: 'app-dashboard',
    templateUrl: './dashboard.html',
    styleUrls: ['./dashboard.component.scss'],
    imports: [RouterModule, RippleModule, MenuModule , StyleClassModule, ButtonModule, DividerModule],
})
export class Dashboard {
    items: MenuItem[] = [];
    constructor(public router: Router) { }
    ngOnInit() {
        this.items = [
            {
                label: 'الملف الشخصي',
                icon: 'pi pi-user',
                command: () => {
                    this.router.navigate(['/profile']);
                }
            },
            {
                label: 'إنشاء حساب',
                icon: 'pi pi-user-plus',
                command: () => {
                    this.router.navigate(['/auth/register']);
                }
            },
            {
                label: 'تسجيل دخول',
                icon: 'pi pi-sign-in',
                command: () => {
                    this.router.navigate(['/auth/login']);
                }
            }
        ];
    }

    navigateTo(route: string[]): void {
        this.router.navigate(route);
    }

    openQuestionnaire(): void {
        const childId = this.getStoredChildId();
        this.router.navigate(['/questionnaire', childId ?? 1]);
    }

    private getStoredChildId(): number | null {
        const rawValue = localStorage.getItem('selected_child_id') ?? localStorage.getItem('child_id');
        const childId = Number(rawValue);
        return Number.isFinite(childId) && childId > 0 ? childId : null;
    }
    
    openTraining(): void {

        const childId = this.getStoredChildId();

        this.router.navigate(['/training'], {
            queryParams: {
                childId: childId,
                difficultyId: 1
            }
        });
    }
}
