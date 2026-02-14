import { Component } from '@angular/core';
import { StyleClassModule } from 'primeng/styleclass';
import { Router, RouterModule } from '@angular/router';
import { RippleModule } from 'primeng/ripple';
import { ButtonModule } from 'primeng/button';
import { AppFloatingConfigurator } from "@/layout/component/app.floatingconfigurator";
import { MenuModule } from 'primeng/menu';
import { MenuItem } from 'primeng/api';

@Component({
    selector: 'topbar-widget',
    imports: [RouterModule, StyleClassModule, ButtonModule, RippleModule, AppFloatingConfigurator,MenuModule],
    template: `
<div class="flex items-center justify-between w-full h-16 px-5 lg:px-8 relative" dir="ltr">

  <div class="flex items-center gap-4 shrink-0">
    <button pButton pRipple icon="pi pi-users" class="text-2xl" [rounded]="true" (click)="menu.toggle($event)"></button>
    <p-menu #menu [model]="items" [popup]="true" styleClass="rounded-xl shadow-lg"></p-menu>
    <app-floating-configurator [float]="false"></app-floating-configurator>
  </div>

  <div class="absolute left-1/2 -translate-x-1/2">
    <ul class="list-none p-0 m-0 flex items-center select-none gap-8">
      <li>
        <a (click)="router.navigate(['/landing'], { fragment: 'التدريبات' })"
           pRipple
           class="px-4 py-2 text-surface-900 dark:text-surface-0 font-medium text-xl hover:bg-[#ECFDF5] hover:text-[#10B981] rounded-full transition-all duration-200">
          التدريبات
        </a>
      </li>
      <li>
        <a (click)="router.navigate(['/landing'], { fragment: 'النتائج' })"
           pRipple
           class="px-4 py-2 text-surface-900 dark:text-surface-0 font-medium text-xl hover:bg-[#ECFDF5] hover:text-[#10B981] rounded-full transition-all duration-200">
          النتائج
        </a>
      </li>
      <li>
        <a (click)="router.navigate(['/landing'], { fragment: 'التقييم' })"
           pRipple
           class="px-4 py-2 text-surface-900 dark:text-surface-0 font-medium text-xl hover:bg-[#ECFDF5] hover:text-[#10B981] rounded-full transition-all duration-200">
          التقييم
        </a>
      </li>
      <li>
        <a (click)="router.navigate(['/landing'], { fragment: ' التعرف على صعوبات التعلم' })"
           pRipple
           class="px-4 py-2 text-surface-900 dark:text-surface-0 font-medium text-xl hover:bg-[#ECFDF5] hover:text-[#10B981] rounded-full transition-all duration-200">
          التعرف على صعوبات التعلم
        </a>
      </li>
    </ul>
  </div>

  <div class="flex items-center gap-2 shrink-0">
    <span class="text-2xl font-bold text-surface-900 dark:text-surface-0">بوصلة</span>
    <div class="h-14 w-14 rounded-full flex items-center justify-center">
      <img src="assets/images/app-logo.png" alt="compass" class="h-10 w-10">
    </div>
  </div>

</div>

        `
})
export class TopbarWidget {
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
