import { Component } from '@angular/core';
import { MenuItem } from 'primeng/api';
import { RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { StyleClassModule } from 'primeng/styleclass';
import { AppConfigurator } from '../../../../layout/component/app.configurator';
import { LayoutService } from '../../../../layout/service/layout.service';
import { Button } from "primeng/button";

@Component({
    selector: 'app-topbar',
    standalone: true,
    imports: [RouterModule, CommonModule, StyleClassModule, Button],
    templateUrl: './app-topbar.html',
})

export class AppTopbar {
    items!: MenuItem[];

    constructor(public layoutService: LayoutService) { }

    toggleDarkMode() {
        this.layoutService.layoutConfig.update((state) => ({ ...state, darkTheme: !state.darkTheme }));
    }
}
