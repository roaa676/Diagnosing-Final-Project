import { Component } from '@angular/core';

@Component({
    selector: 'footer-widget',
    imports: [],
    template: `
        <footer class="py-10 px-6 lg:px-16 mt-16" dir="ltr">
            <div class="flex flex-col gap-10 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-4">

  <span class="h-11 w-11 rounded-full bg-emerald-50 flex items-center justify-center">
    <i class="pi pi-users text-emerald-700 text-xl"></i>
  </span>

  <span class="h-11 w-11 rounded-full bg-emerald-50 flex items-center justify-center">
    <i class="pi pi-at text-emerald-700 text-xl"></i>
  </span>

  <span class="h-11 w-11 rounded-full bg-emerald-50 flex items-center justify-center">
    <i class="pi pi-globe text-emerald-700 text-xl"></i>
  </span>

</div>


                <div class="text-center lg:text-right text-emerald-700 space-y-2 text-lg">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:gap-12 font-semibold">
                        <a class="cursor-pointer">اتصل بنا</a>
                        <a class="cursor-pointer">الأسئلة الشائعة</a>
                        <a class="cursor-pointer">بوتات للدردشة</a>
                        <a class="cursor-pointer">خيارات مساعدة المستخدم</a>
                    </div>
                </div>

<div class="flex items-center gap-3">

  <h4 class="text-2xl font-bold text-surface-900 leading-none m-0">
    بوصلة
  </h4>

  <div class="h-12 w-12 flex items-center justify-center -translate-y-2">
    <img src="assets/images/app-logo.png"
         alt="compass"
         class="h-10 w-10 object-contain">
  </div>

</div>

            </div>
        </footer>
    `
})
export class FooterWidget {
}
