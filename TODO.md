# TODO

## Task: خلي Assessment نفس UI شاشه Training game بالظبططط

- [x] فهم المطلوب: Assessment screen الحالي شكله مختلف (assessment-container + header-bar etc) بينما Training game عنده layout موحد (game-container + game-header + question-card + options-grid + result-card)

- [ ] تعديل `src/app/pages/assessment/assessment.component.html` ليطابق هيكل `training-game.component.html` قدر الإمكان (نفس class names + نفس تقسيم الأقسام loading/error/question/result)
- [ ] تعديل `src/app/pages/assessment/assessment.component.css` ليرث نفس ستايلات Training game (أو استبدال CSS بالكامل لتطابقها) مع الحفاظ على أي اختلافات لازمة للنتائج/الأزرار
- [ ] التأكد من وجود properties/methods المستخدمة في الـ HTML: لازم نستخدم نفس أسماء الـ handlers الموجودة في Assessment (مثلاً nextQuestion/previousQuestion بدل next في Training) أو نعمل mapping/aliases داخل component إذا لزم
- [ ] تحديث `assessment.component.ts` لإضافة aliases/guards بحيث الـ template الجديد يشتغل بدون أخطاء (مثل: goBack, selectOption, isSelected, isLastQuestion, progressPercent, helperMessage, formattedTime, currentQuestionIndex…)
- [ ] اختبار سريع: تشغيل build/serve + التأكد من عدم وجود errors في template

