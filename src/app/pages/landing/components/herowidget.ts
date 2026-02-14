import { Component } from '@angular/core';
import { ButtonModule } from 'primeng/button';
import { RippleModule } from 'primeng/ripple';

@Component({
    selector: 'hero-widget',
    imports: [ButtonModule, RippleModule],
    template: `
<div
    id="hero"
    class="flex flex-col lg:flex-row-reverse items-center justify-between px-6 lg:px-20 py-12 overflow-hidden"
    style="background-color: #f8fafc;"
>


    <div class="lg:w-1/2 text-right">

        <div class="inline-flex items-center gap-2
            bg-green-100 text-green-700
            px-5 py-2
            rounded-full
            text-lg font-semibold
            mb-6">

    باقي في رحلة التعلم
    <span class="w-2.5 h-2.5 bg-green-600 rounded-full"></span>

</div>


        <h1 class="text-4xl lg:text-5xl font-bold leading-tight text-gray-900">
            نكتشف صعوبات التعلم
            <span class="text-[#4C9A6B] block mt-2">
                وندعم مستقبل طفلك
            </span>
        </h1>

        <p class="text-gray-600 text-lg mt-6 leading-relaxed">
            تطبيق "بوصلة" هو منصة متكاملة تهدف إلى تشخيص صعوبات التعلم عند الأطفال مبكرًا،
            وتقديم الدعم من خلال تدريبات تفاعلية ومقترحات تربوية متخصصة لبناء مستقبل أفضل لأطفالكم.
        </p>

        <button
    class="mt-8 px-10 py-4 text-xl font-semibold
           bg-[#10B981] hover:bg-[#059669]
           text-white border-0
           rounded-full
           transition-all duration-300">
    ابدأ الرحلة
</button>




    </div>

    <div class="lg:w-1/2 flex justify-center mt-10 lg:mt-0">

        <div class="w-full max-w-md h-96 bg-gray-200 rounded-3xl shadow-lg">
            <img src="/assets/images/homeIMG.png"   class="w-full h-full object-cover rounded-3xl" />
        </div>

    </div>

</div>

    `
})
export class HeroWidget { }
