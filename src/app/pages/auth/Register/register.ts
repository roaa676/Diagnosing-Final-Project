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

  // بيانات ولي الأمر
  fullName: string = '';
  email: string = '';

  // بيانات الطفل
  childName: string = '';
  childAge: number | null = null;

  // الأمان
  password: string = '';
  confirmPassword: string = '';

  register() {

    // تحقق بسيط قبل الإرسال
    if (!this.fullName || !this.email || !this.childName || !this.childAge || !this.password || !this.confirmPassword) {
      alert('من فضلك املئي جميع البيانات ');
      return;
    }

    if (this.password !== this.confirmPassword) {
      alert('كلمتا السر غير متطابقتين');
      return;
    }

    console.log('Register Data:', {
      fullName: this.fullName,
      email: this.email,
      childName: this.childName,
      childAge: this.childAge,
      password: this.password
    });

    alert('تم إنشاء الحساب بنجاح 🎉');
  }
}

  export class Register {

  children = [
    { name: '', age: '' }
  ];

  addChild() {
    console.log("clicked");   // عشان نتأكد إنها بتتنفذ
    this.children.push({ name: '', age: '' });
  }

}