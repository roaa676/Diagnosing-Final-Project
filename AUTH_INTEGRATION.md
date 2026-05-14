# Authentication Integration Guide

## ملخص التحديثات

تم ربط APIs الـ Authentication الخاصة بـ Laravel مع Angular components للـ Login و Register.

## الملفات المنشأة/المحدثة:

### 1. **auth.service.ts** (جديد)
- مسؤول عن جميع طلبات الـ Authentication
- يحفظ الـ token في localStorage
- يوفر methods للـ login و register و logout

**Methods:**
- `login(email, password)` - تسجيل الدخول
- `register(fullName, email, password, confirmPassword, children)` - إنشاء حساب جديد
- `getToken()` - الحصول على الـ token المحفوظ
- `isAuthenticated()` - التحقق من تسجيل الدخول
- `logout()` - تسجيل الخروج

### 2. **auth.interceptor.ts** (جديد)
- يضيف الـ Bearer token تلقائياً مع جميع الطلبات
- يتعامل مع الـ 401 errors (Unauthorized) بتسجيل الخروج والعودة للـ login

### 3. **login.ts** - تحديثات
- إضافة AuthService
- التحقق من صحة البيانات
- عرض حالة التحميل (isLoading)
- معالجة الأخطاء من الـ API

### 4. **register.ts** - تحديثات
- إضافة AuthService
- التحقق من أن كلمات السر متطابقة
- التحقق من أن جميع بيانات الأطفال مملوءة
- عرض حالة التحميل
- معالجة الأخطاء

### 5. **app.config.ts** - تحديثات
- تفعيل AuthInterceptor

### 6. **HTML Templates** - تحديثات
- إضافة disabled و loading state للزر عند التحميل

## Endpoints:

```
POST /api/login
- Body: { email, password }
- Response: { token, message, user }

POST /api/register
- Body: { 
    name, 
    email, 
    password, 
    password_confirmation, 
    children: [{ name, age }]
  }
- Response: { token, message, user }
```

## الملاحظات المهمة:

1. **childName** في الـ login form لا يُرسل للـ API حالياً - يمكن حذفه من الـ form إذا لم يكن مطلوباً
2. جميع الطلبات المحمية تحتاج إلى Authorization header مع الـ token (يتم إضافته تلقائياً بواسطة الـ Interceptor)
3. الـ token يُحفظ في localStorage - للـ production يُفضل استخدام httpOnly cookies
4. عند انتهاء صلاحية الـ token (401 error)، يتم تسجيل الخروج تلقائياً
