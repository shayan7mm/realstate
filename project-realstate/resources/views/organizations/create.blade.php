<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ثبت ارگان جدید</title>
    <link rel="stylesheet" href="{{ asset('css/Style.css') }}">
</head>
<body>

    <div class="bg-wrapper"></div>

    <div class="container">

        <header class="header">
            <div class="header-left">
                <div class="logo-box">حس</div>
                <div>
                    <h1>ثبت ارگان جدید</h1>
                    <div class="subtitle">افزودن ارگان به سیستم</div>
                </div>
            </div>
            <div class="header-right">
                <div class="user-badge">
                    <div class="avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    <div class="user-meta">
                        <div class="name">{{ auth()->user()->name }}</div>
                        <div class="role">{{ auth()->user()->isMaster() ? 'مدیر ارشد' : 'حسابدار' }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-logout">خروج</button>
                </form>
            </div>
        </header>

        <a href="{{ route('organizations.index') }}" class="back-link">بازگشت به لیست ارگان‌ها</a>

        <div class="form-card">
            <form method="POST" action="{{ route('organizations.store') }}">
                @csrf

                <div class="form-group">
                    <label for="name">نام ارگان <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="مثال: بانک سامان" required>
                    @error('name')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">توضیحات (اختیاری)</label>
                    <textarea id="description" name="description" rows="3" style="width:100%; padding:14px 18px; background:#f8fafc; border:2px solid #e5e7eb; border-radius:14px; color:#1a1a2e; font-size:15px; outline:none; transition:all 0.3s; resize:vertical; font-family:inherit;">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-actions">
                    <a href="{{ route('organizations.index') }}" class="btn-cancel">بازگشت</a>
                    <button type="submit" class="btn-submit">ثبت ارگان</button>
                </div>
            </form>
        </div>

    </div>

</body>
</html>