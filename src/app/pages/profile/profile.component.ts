import { ChildService } from '@/core/services/child.service';
import { ProfileService } from '@/core/services/profile.service';
import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { AvatarModule } from 'primeng/avatar';
import { ButtonModule } from 'primeng/button';
import { InputTextModule } from 'primeng/inputtext';
import { PasswordModule } from 'primeng/password';
import { DialogModule } from 'primeng/dialog';

type SectionKey = 'personal-info' | 'children' | 'support' | 'notifications';
type NavTone = 'green' | 'purple' | 'blue' | 'gray';

interface NavigationItem {
    label: string;
    fragment: SectionKey;
    icon: string;
    tone: NavTone;
}

interface ChildProfile {
    name: string;
    details: string;
    avatar: string;
}

interface SupportItem {
    label: string;
    icon: string;
}

interface NotificationSetting {
    id: string;
    title: string;
    description: string;
    enabled: boolean;
}

@Component({
    selector: 'app-profile',
    standalone: true,
    imports: [CommonModule, ReactiveFormsModule, RouterModule, ButtonModule, AvatarModule, InputTextModule, PasswordModule, DialogModule],
    templateUrl: './profile.component.html',
    styleUrls: ['./profile.component.css']
})
export class ProfileComponent implements OnInit {
    readonly navigationItems: NavigationItem[] = [
        {
            label: 'المعلومات الشخصية',
            fragment: 'personal-info',
            icon: 'pi pi-user',
            tone: 'green'
        },
        {
            label: 'إدارة الأطفال',
            fragment: 'children',
            icon: 'pi pi-users',
            tone: 'purple'
        },
        {
            label: 'الدعم والمساعدة',
            fragment: 'support',
            icon: 'pi pi-question-circle',
            tone: 'blue'
        },
        {
            label: 'الإشعارات',
            fragment: 'notifications',
            icon: 'pi pi-bell',
            tone: 'gray'
        }
    ];

    childProfiles: ChildProfile[] = [];
    showAddChildDialog = false;
    readonly supportItems: SupportItem[] = [
        {
            label: 'الأسئلة الشائعة',
            icon: 'pi pi-question-circle'
        },
    ];

    readonly notificationSettings: NotificationSetting[] = [
        {
            id: 'progress',
            title: 'إشعارات التقدم',
            description: 'عند إكمال مستوى جديد أو تحقيق إنجاز',
            enabled: true
        },
        {
            id: 'email',
            title: 'إشعارات البريد',
            description: 'عند استلام رسائل أو تحديثات مهمة',
            enabled: true
        }
    ];

    activeSection: SectionKey = 'personal-info';

    profileForm = new FormGroup({
        guardianName: new FormControl('أحمد علي'),
        email: new FormControl('ahmed@example.com'),
        password: new FormControl('12345678')
    });
    childForm = new FormGroup({
        name: new FormControl(''),
        age: new FormControl('')
    });

    constructor(private readonly router: Router,
        private readonly profileService: ProfileService,
        private readonly childService: ChildService
    ) { }

    ngOnInit(): void {
        this.loadProfile();
        this.loadChildren();
    }

    loadProfile(): void {
        this.profileService.getProfile().subscribe({
            next: (res) => {
                if (res.data) {
                    this.profileForm.patchValue({
                        guardianName: res.data.name,
                        email: res.data.email
                    });
                }
            },
            error: (err: any) => console.error(err)
        });
    }

    loadChildren(): void {
        this.childService.getAllChildren().subscribe({
            next: (res) => {

                this.childProfiles = (res.data ?? []).map(child => ({
                    name: child.name,
                    details: `${child.age} سنوات`,
                    avatar: child.image
                        ? `http://127.0.0.1:8000/${child.image}`
                        : 'assets/images/boy.png'
                }));

            },
            error: (err: any) => console.error(err)
        });
    }

    setActiveSection(section: SectionKey): void {
        this.activeSection = section;
    }

    navButtonClass(item: NavigationItem): string {
        const base = ['profile-nav__button', `profile-nav__button--${item.tone}`];

        if (this.activeSection === item.fragment) {
            base.push('profile-nav__button--active');
        }

        return base.join(' ');
    }

    saveProfile(): void {

        const payload = {
            name: this.profileForm.value.guardianName ?? '',
            email: this.profileForm.value.email ?? ''
        };

        this.profileService.updateProfile(payload).subscribe({
            next: () => {
                alert('تم حفظ البيانات بنجاح');
            },
            error: (err: any) => {
                console.error(err);
            }
        });
    }

    trackByChild(_: number, child: ChildProfile): string {
        return child.name;
    }

    addChild(): void {

        this.showAddChildDialog = true;
    }
    editChild(child: ChildProfile): void {
        console.log('Edit child:', child.name);
    }
    saveChild(): void {

        const name = this.childForm.value.name ?? '';
        const age = Number(this.childForm.value.age);

        this.childService.createChild(name, age).subscribe({
            next: () => {

                this.loadChildren();

                this.childForm.reset();

                this.showAddChildDialog = false;
            },
            error: (err: any) => {
                console.error(err);
            }
        });
    }

    openSupportItem(): void {
        const chatButton = document.querySelector(
            'button.chat-bubble'
        ) as HTMLElement | null;

        chatButton?.click();
    }


    trackByNotification(_: number, item: NotificationSetting): string {
        return item.id;
    }

    toggleNotification(setting: NotificationSetting): void {
        setting.enabled = !setting.enabled;
    }

    logout(): void {
        this.router.navigate(['/login']);
    }
}
