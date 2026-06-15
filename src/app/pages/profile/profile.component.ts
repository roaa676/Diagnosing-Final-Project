import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormControl, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { AvatarModule } from 'primeng/avatar';
import { ButtonModule } from 'primeng/button';
import { InputTextModule } from 'primeng/inputtext';
import { PasswordModule } from 'primeng/password';

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
    imports: [CommonModule, ReactiveFormsModule, RouterModule, ButtonModule, AvatarModule, InputTextModule, PasswordModule],
    templateUrl: './profile.component.html',
    styleUrls: ['./profile.component.css']
})
export class ProfileComponent {
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

    readonly childProfiles: ChildProfile[] = [
        {
            name: 'عمر',
            details: '8 سنوات • المستوى 1',
            avatar: 'assets/images/boy.png'
        },
        {
            name: 'ليلى',
            details: '6 سنوات • المستوى 2',
            avatar: 'assets/images/Girl.png'
        }
    ];

    readonly supportItems: SupportItem[] = [
        {
            label: 'الأسئلة الشائعة',
            icon: 'pi pi-question-circle'
        },
        {
            label: 'تواصل معنا',
            icon: 'pi pi-envelope'
        }
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

    constructor(private readonly router: Router) {}

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
        console.log('Saving profile:', this.profileForm.getRawValue());
    }

    trackByChild(_: number, child: ChildProfile): string {
        return child.name;
    }

    addChild(): void {
        console.log('Add child requested');
    }

    editChild(child: ChildProfile): void {
        console.log('Edit child:', child.name);
    }

    openSupportItem(item: SupportItem): void {
        console.log('Support item:', item.label);
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
