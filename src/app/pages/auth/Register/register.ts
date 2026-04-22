import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { ButtonModule } from 'primeng/button';
import { InputTextModule } from 'primeng/inputtext';
import { PasswordModule } from 'primeng/password';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [
    FormsModule,
    RouterModule,
    ButtonModule,
    InputTextModule,
    PasswordModule
  ],
  templateUrl: './register.html',
  styleUrls: ['../auth.css']
})
export class RegisterComponent {

  constructor(private router: Router) { }

  parentName = '';
  email = '';
  childName = '';
  age = '';
  password = '';
  confirmPassword = '';

  register() {
    console.log('Registered');

    this.router.navigate(['/dashboard']);
  }
}
