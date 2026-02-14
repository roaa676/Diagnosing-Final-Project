import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
    selector: 'features-widget',
    standalone: true,
    imports: [CommonModule],
    template: ` <div id="features" class="py-6 px-6 lg:px-20 mt-8 mx-0 lg:mx-20">
        <div class="grid grid-cols-12 gap-4 justify-center">
            <div class="col-span-12 text-center mt-20 mb-6">
                <div class="text-surface-900 dark:text-surface-0 font-normal mb-2 text-4xl">الخدمات التى يقدمها المشروع</div>
                <span class="text-[#4C9A6B] text-2xl">مجموعه متكامله من الادوات العلميه و التربوية لمساعده طفلك</span>
            </div>

            <div class="col-span-12 md:col-span-12 lg:col-span-3 p-0 lg:pr-8 lg:pb-8 mt-6 lg:mt-0">
                <div style="height: 160px; padding: 2px; border-radius: 10px; background: linear-gradient(90deg, rgba(253, 228, 165, 0.2), rgba(187, 199, 205, 0.2)), linear-gradient(180deg, rgba(253, 228, 165, 0.2), rgba(187, 199, 205, 0.2))">
                    <div class="p-4 bg-surface-0 dark:bg-surface-900 h-full flex flex-col items-center justify-center text-center" style="border-radius: 8px">

                        <div class="flex items-center justify-center bg-yellow-200 mb-4" style="width: 3.5rem; height: 3.5rem; border-radius: 10px">
                            <i class="pi pi-fw pi-chart-line text-2xl! text-yellow-700"></i>
                        </div>
                        <h5 class="mb-2 text-surface-900 dark:text-surface-0">نتائج و تحليل مبسط</h5>
                        <span class="text-[#4C9A6B] dark:text-surface-200">تقارير و رسوم بيانية سهلة الفهم توضح مسار التقدم</span>
                    </div>
                </div>
            </div>

            <div class="col-span-12 md:col-span-12 lg:col-span-3 p-0 lg:pr-8 lg:pb-8 mt-6 lg:mt-0">
                <div style="height: 160px; padding: 2px; border-radius: 10px; background: linear-gradient(90deg, rgba(145, 226, 237, 0.2), rgba(251, 199, 145, 0.2)), linear-gradient(180deg, rgba(253, 228, 165, 0.2), rgba(172, 180, 223, 0.2))">
                    <div class="p-4 bg-surface-0 dark:bg-surface-900 h-full flex flex-col items-center justify-center text-center" style="border-radius: 8px">

                        <div class="flex items-center justify-center bg-cyan-200 mb-4" style="width: 3.5rem; height: 3.5rem; border-radius: 10px">
                            <i class="pi pi-fw pi-lightbulb text-2xl! text-cyan-700"></i>
                        </div>
                        <h5 class="mb-2 text-surface-900 dark:text-surface-0">اختبارات تدريبيه للطفل</h5>
                        <span class="text-[#4C9A6B] dark:text-surface-200">تدريبات تفاعلية ممتعة لمتابعة حالة الطفل</span>
                    </div>
                </div>
            </div>

            <div class="col-span-12 md:col-span-12 lg:col-span-3 p-0 lg:pb-8 mt-6 lg:mt-0">
                <div style="height: 160px; padding: 2px; border-radius: 10px; background: linear-gradient(90deg, rgba(145, 226, 237, 0.2), rgba(172, 180, 223, 0.2)), linear-gradient(180deg, rgba(172, 180, 223, 0.2), rgba(246, 158, 188, 0.2))">
                    <div class="p-4 bg-surface-0 dark:bg-surface-900 h-full flex flex-col items-center justify-center text-center" style="border-radius: 8px">

                        <div class="flex items-center justify-center bg-indigo-200" style="width: 3.5rem; height: 3.5rem; border-radius: 10px">
                            <i class="pi pi-fw pi-file text-2xl! text-indigo-700"></i>
                        </div>
                        <div class="mt-6 mb-1 text-surface-900 dark:text-surface-0 text-xl font-semibold">الاستبيانات</div>
                        <span class="text-[#4C9A6B] dark:text-surface-200">جمع معلومات دقيقة من الآباء و المعلمين لفهم الحالة</span>
                    </div>
                </div>
            </div>

            <div class="col-span-12 md:col-span-12 lg:col-span-3 p-0 lg:pr-8 lg:pb-8 mt-6 lg:mt-0">
                <div style="height: 160px; padding: 2px; border-radius: 10px; background: linear-gradient(90deg, rgba(187, 199, 205, 0.2), rgba(251, 199, 145, 0.2)), linear-gradient(180deg, rgba(253, 228, 165, 0.2), rgba(145, 210, 204, 0.2))">
                    <div class="p-4 bg-surface-0 dark:bg-surface-900 h-full flex flex-col items-center justify-center text-center" style="border-radius: 8px">

                        <div class="flex items-center justify-center bg-slate-200 mb-4" style="width: 3.5rem; height: 3.5rem; border-radius: 10px">
                            <i class="pi pi-fw pi-id-card text-2xl! text-slate-700"></i>
                        </div>
                        <div class="mt-6 mb-1 text-surface-900 dark:text-surface-0 text-xl font-semibold">التقييم المبدئى</div>
                        <span class="text-[#4C9A6B] dark:text-surface-200">تحديد مستوى الطفل الحالى و نقاط البداية المناسبة</span>
                    </div>
                </div>
            </div>



            <div
                class="col-span-12 mt-20 mb-20 p-2 md:p-20"
                style="border-radius: 20px; background: #0D1B13;"
>
                <div class="flex flex-col justify-center items-center text-center px-4 py-4 md:py-0">
                    <div class="text-white mb-2 text-3xl font-semibold">هل انت مستعد لمساعدة طفلك فى رحلة النجاح ؟</div>
                    <span class="text-white text-2xl">ابدأ الآن فى اكتشاف قدرات طفلك و معالجه صعوبات اتعلم بأحدث الأساليب العلمية</span>
                    <button
    class="mt-8 px-10 py-4 text-xl font-semibold
           bg-[#10B981] hover:bg-[#059669]
           text-white border-0
           rounded-full
           transition-all duration-300">
    ابدأ الرحلة
</button>


                </div>
            </div>
        </div>
    </div>`
})
export class FeaturesWidget { }
