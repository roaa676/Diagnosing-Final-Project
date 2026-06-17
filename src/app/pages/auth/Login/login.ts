import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { ButtonModule } from 'primeng/button';
import { InputTextModule } from 'primeng/inputtext';
import { PasswordModule } from 'primeng/password';
import { AuthService } from '../../../core/services/auth.service';
import { ChildService } from '@/core/services/child.service';

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

    constructor(
        private router: Router,
        private authService: AuthService,
        private childService: ChildService
    ) { }

    email: string = '';
    childName: string = '';
    password: string = '';
    isLoading: boolean = false;

    login() {
        if (!this.email || !this.password) {
            alert('من فضلك أملي جميع البيانات');
            return;
        }

        this.isLoading = true;
        this.authService.login(this.email, this.password).subscribe({
            next: (response: any) => {
                this.isLoading = false;

                localStorage.setItem('auth_token', response.token);

                this.childService.getAllChildren().subscribe({
                    next: (childrenResponse: any) => {

                        const firstChild = childrenResponse?.data?.[0];

                        if (firstChild) {
                            localStorage.setItem(
                                'selected_child_id',
                                firstChild.id.toString()
                            );
                        }

                        alert('تم تسجيل الدخول بنجاح');
                        this.router.navigate(['/dashboard']);
                    },
                    error: () => {
                        this.router.navigate(['/dashboard']);
                    }
                });
            },
            error: (error) => {
                this.isLoading = false;
                console.error('Login error:', error);
                alert(error.error?.message || 'فشل تسجيل الدخول');
            }
        });
    }
}
