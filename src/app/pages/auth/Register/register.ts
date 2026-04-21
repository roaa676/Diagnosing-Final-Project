import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common'; 
import { ButtonModule } from 'primeng/button';
import { InputTextModule } from 'primeng/inputtext';
import { PasswordModule } from 'primeng/password';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [
    CommonModule,   
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

  
  fullName: string = '';
  email: string = '';

  
  children = [
    { name: '', age: null }
  ];

  // الأمان
  password: string = '';
  confirmPassword: string = '';

  addChild() {
    console.log("clicked");
    this.children.push({ name: '', age: null });
  }

  register() {

    if (!this.fullName || !this.email || !this.password || !this.confirmPassword) {
      alert('من فضلك املئي جميع البيانات');
      return;
    }

    if (this.password !== this.confirmPassword) {
      alert('كلمتا السر غير متطابقتين');
      return;
    }

    console.log('Register Data:', {
      fullName: this.fullName,
      email: this.email,
      children: this.children,
      password: this.password
    });

    alert('تم إنشاء الحساب بنجاح 🎉');
  }
}