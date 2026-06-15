import { Component } from '@angular/core';
import { RouterModule } from '@angular/router';
import { ChatbotComponent } from '@/pages/chatbot/chatbot.component';

@Component({
    selector: 'app-root',
    standalone: true,
    imports: [RouterModule, ChatbotComponent],
    template: `<router-outlet></router-outlet><app-chatbot></app-chatbot>`
})
export class AppComponent {}
