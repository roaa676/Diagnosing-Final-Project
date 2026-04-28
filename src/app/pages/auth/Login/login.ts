import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { ButtonModule } from 'primeng/button';
import { InputTextModule } from 'primeng/inputtext';
import { PasswordModule } from 'primeng/password';

@Component({
    selector: 'app-login',
    standalone: true,
    imports: [
        ButtonModule,
        InputTextModule,
        FormsModule,
        RouterModule,
        PasswordModule
    ],
    templateUrl: './login.html',
    styleUrl: '../auth.css',

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
