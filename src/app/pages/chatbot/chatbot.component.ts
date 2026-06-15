import { Component, ElementRef, ViewChild, AfterViewChecked } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpErrorResponse } from '@angular/common/http';
import { ChatbotService, ChatMessage } from '@/core/services/chatbot.service';
import { AuthService } from '@/core/services/auth.service';

@Component({
  selector: 'app-chatbot',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './chatbot.component.html',
  styleUrl: './chatbot.component.css'
})
export class ChatbotComponent implements AfterViewChecked {
  @ViewChild('messagesContainer') private messagesContainer!: ElementRef;

  isOpen = false;
  isLoading = false;
  userInput = '';
  messages: ChatMessage[] = [];

  constructor(
    private chatbotService: ChatbotService,
    private authService: AuthService
  ) {}

  toggleChat() {
    this.isOpen = !this.isOpen;
  }

  resetChat() {
    this.messages = [];
    this.userInput = '';
    this.isLoading = false;
  }

  quickAsk(text: string) {
    this.userInput = text;
    this.send();
  }

  send() {
    const text = this.userInput.trim();
    if (!text || this.isLoading) return;

    this.messages.push({ role: 'user', text, timestamp: new Date() });
    this.userInput = '';
    this.isLoading = true;

    const childId = this.getChildId();

    this.chatbotService.ask(text, childId ?? undefined).subscribe({
      next: (res) => {
        this.messages.push({ role: 'assistant', text: res.answer ?? 'عذراً، لم أفهم السؤال.', timestamp: new Date() });
        this.isLoading = false;
      },
      error: (err: HttpErrorResponse) => {
        let text = 'حدث خطأ مؤقت. يرجى المحاولة مرة أخرى.';
        if (err.status === 401 || !this.authService.isAuthenticated()) {
          text = 'يجب تسجيل الدخول أولاً للتحدث مع المساعد.';
        } else if (err.status === 0) {
          text = 'لا يمكن الوصول للخادم. تأكد من اتصالك بالإنترنت.';
        }
        this.messages.push({ role: 'assistant', text, timestamp: new Date() });
        this.isLoading = false;
      }
    });
  }

  formatText(text: string): string {
    return text
      .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
      .replace(/\n/g, '<br>');
  }

  private getChildId(): number | null {
    const raw = localStorage.getItem('selected_child_id') ?? localStorage.getItem('child_id');
    const parsed = raw ? parseInt(raw, 10) : null;
    return parsed && !isNaN(parsed) ? parsed : null;
  }

  ngAfterViewChecked() {
    this.scrollToBottom();
  }

  private scrollToBottom() {
    try {
      const el = this.messagesContainer?.nativeElement;
      if (el) el.scrollTop = el.scrollHeight;
    } catch {}
  }
}
