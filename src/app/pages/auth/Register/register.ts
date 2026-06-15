import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common'; 
import { ButtonModule } from 'primeng/button';
import { InputTextModule } from 'primeng/inputtext';
import { PasswordModule } from 'primeng/password';
import { AuthService } from '../../../core/services/auth.service';

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

  constructor(
    private router: Router,
    private authService: AuthService
  ) { }

  
  fullName: string = '';
  email: string = '';

  
  children = [
    { name: '', age: null }
  ];

  password: string = '';
  confirmPassword: string = '';
  isLoading: boolean = false;

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

    // Validate that all children have names and ages
    for (let child of this.children) {
      if (!child.name || !child.age) {
        alert('من فضلك املئي بيانات جميع الأطفال');
        return;
      }
    }

    this.isLoading = true;
    this.authService.register(this.fullName, this.email, this.password, this.confirmPassword, this.children)
      .subscribe({
        next: (response) => {
          this.isLoading = false;
          alert('تم إنشاء الحساب بنجاح 🎉');
          console.log('Registration successful:', response);
          this.router.navigate(['/dashboard']);
        },
        error: (error) => {
          this.isLoading = false;
          console.error('Registration error:', error);
          alert(error.error?.message || 'فشل إنشاء الحساب');
        }
      });
  }
}
