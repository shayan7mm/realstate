<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ثبت‌نام حسابدار جدید</title>
    <link rel="stylesheet" href="{{ asset('css/Style.css') }}">
</head>
<body>

    <div class="bg-wrapper"></div>

    <div class="container">

        <header class="header">
            <div class="header-left">
                <div class="logo-box">حس</div>
                <div>
                    <h1>ثبت‌نام حسابدار</h1>
                    <div class="subtitle">ایجاد کاربر جدید</div>
                </div>
            </div>
            <div class="header-right">
                <div class="user-badge">
                    <div class="avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    <div class="user-meta">
                        <div class="name">{{ auth()->user()->name }}</div>
                        <div class="role">مدیر ارشد</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout">خروج از حساب</button>
                </form>
            </div>
        </header>

        <a href="{{ route('master.users') }}" class="back-link">بازگشت به لیست حسابداران</a>

        <div class="form-card">
            <form method="POST" action="{{ route('master.users.store') }}">
                @csrf

                <div class="form-group">
                    <label for="name">نام کامل</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="نام و نام خانوادگی" required>
                    @error('name')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">ایمیل</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="example@email.com" required>
                    @error('email')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">شماره موبایل</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="09123456789" dir="ltr" required>
                    <div class="help-text">مثال: 09123456789</div>
                    @error('phone')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">رمز عبور</label>
                    <input type="password" id="password" name="password" placeholder="حداقل ۸ کاراکتر" required>
                    <div class="help-text">حداقل ۸ کاراکتر</div>
                    @error('password')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">تکرار رمز عبور</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="تکرار رمز عبور" required>
                </div>

                <div class="form-actions">
                    <a href="{{ route('master.users') }}" class="btn-cancel">بازگشت</a>
                    <button type="submit" class="btn-submit">ثبت‌نام حسابدار</button>
                </div>
            </form>
        </div>

    </div>

</body>
</html>