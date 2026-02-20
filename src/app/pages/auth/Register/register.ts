import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
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

  parentName = '';
  email = '';
  childName = '';
  age = '';
  password = '';
  confirmPassword = '';

  register() {
    console.log({
      parentName: this.parentName,
      email: this.email,
      childName: this.childName,
      age: this.age,
      password: this.password
    });
  }
}