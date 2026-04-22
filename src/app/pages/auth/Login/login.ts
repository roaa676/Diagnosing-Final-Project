import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { ButtonModule } from 'primeng/button';
import { CheckboxModule } from 'primeng/checkbox';
import { InputTextModule } from 'primeng/inputtext';
import { PasswordModule } from 'primeng/password';
import { RippleModule } from 'primeng/ripple';

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
    ],
    templateUrl: './login.html',
    styleUrls: ['../auth.css']

})
export class Login {

    constructor(private router: Router) { }

    email: string = '';
    childName: string = '';
    password: string = '';

    login() {
        console.log('Email:', this.email);

        this.router.navigate(['/dashboard']);
    }
}
