import { Component } from '@angular/core';
import { Router, RouterModule } from '@angular/router';
import { RippleModule } from 'primeng/ripple';
import { StyleClassModule } from 'primeng/styleclass';
import { ButtonModule } from 'primeng/button';
import { DividerModule } from 'primeng/divider';
import { MenuItem } from 'primeng/api';
import { AppFloatingConfigurator } from '@/layout/component/app.floatingconfigurator';
import { MenuModule } from 'primeng/menu';

@Component({
    selector: 'app-dashboard',
    templateUrl: './dashboard.html',
    imports: [RouterModule, RippleModule, MenuModule , StyleClassModule, ButtonModule, DividerModule, AppFloatingConfigurator],
})
export class Dashboard {
    items: MenuItem[] = [];
    constructor(public router: Router) { }
    ngOnInit() {
        this.items = [
            {
                label: 'إنشاء حساب',
                icon: 'pi pi-user-plus',
                command: () => {
                    this.router.navigate(['/register']);
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
}
