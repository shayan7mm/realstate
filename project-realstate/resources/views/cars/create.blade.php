<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ثبت ماشین جدید</title>
    <link rel="stylesheet" href="{{ asset('css/Style.css') }}">
</head>
<body>

    <div class="bg-wrapper"></div>

    <div class="container">

        <header class="header">
            <div class="header-left">
                <div class="logo-box">حس</div>
                <div>
                    <h1>ثبت ماشین جدید</h1>
                    <div class="subtitle">افزودن ماشین به سیستم</div>
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

        <a href="{{ route('cars.index') }}" class="back-link">بازگشت به لیست ماشین‌ها</a>

        <div class="form-card">
            <form method="POST" action="{{ route('cars.store') }}">
                @csrf

                <div class="form-group">
                    <label for="name">اسم ماشین <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="مثال: پراید سفید" required>
                    @error('name')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="owner_name">اسم صاحب ماشین <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="owner_name" name="owner_name" value="{{ old('owner_name') }}" placeholder="نام و نام خانوادگی" required>
                    @error('owner_name')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="owner_phone">شماره تماس صاحب ماشین <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="owner_phone" name="owner_phone" value="{{ old('owner_phone') }}" placeholder="09123456789" dir="ltr" required>
                    @error('owner_phone')
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
                    <a href="{{ route('cars.index') }}" class="btn-cancel">بازگشت</a>
                    <button type="submit" class="btn-submit">ثبت ماشین</button>
                </div>
            </form>
        </div>

    </div>

</body>
</html>