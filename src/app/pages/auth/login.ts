import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { ButtonModule } from 'primeng/button';
import { CheckboxModule } from 'primeng/checkbox';
import { InputTextModule } from 'primeng/inputtext';
import { PasswordModule } from 'primeng/password';
import { RippleModule } from 'primeng/ripple';
import { AppFloatingConfigurator } from '../../layout/component/app.floatingconfigurator';

@Component({
    selector: 'app-login',
    standalone: true,
    imports: [
        ButtonModule,
        CheckboxModule,
        InputTextModule,
        PasswordModule,
        FormsModule,
        RouterModule,
        RippleModule,
        AppFloatingConfigurator
    ],
    templateUrl: './login.html',
    styleUrls: ['./login.css']
})
export class Login {

    email: string = '';
    childName: string = '';
    password: string = '';
    checked: boolean = false;

    login() {
        console.log('Email:', this.email);
        console.log('Child Name:', this.childName);
        console.log('Password:', this.password);
    }
}
