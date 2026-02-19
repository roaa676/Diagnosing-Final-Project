import { AppMenu } from '@/layout/component/app.menu';
import { Component, ElementRef } from '@angular/core';

@Component({
    selector: 'app-sidebar',
    standalone: true,
    imports: [AppMenu],
    templateUrl: `./app-sidebar.html`,
})
export class AppSidebar {
    constructor(public el: ElementRef) { }
}
