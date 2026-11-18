<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ویرایش پروفایل</title>
    <link rel="stylesheet" href="{{ asset('css/Style.css') }}">
</head>
<body>

    <div class="bg-wrapper"></div>

    <div class="container">

        <header class="header">
            <div class="header-left">
                <div class="logo-box">حس</div>
                <div>
                    <h1>ویرایش پروفایل</h1>
                    <div class="subtitle">{{ auth()->user()->isMaster() ? 'مدیر ارشد' : 'حسابدار' }}</div>
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
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout">خروج از حساب</button>
                </form>
            </div>
        </header>

        <a href="{{ route('dashboard') }}" class="back-link">بازگشت به داشبورد</a>

        @if(session('status') == 'profile-updated')
            <div style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.15); border-radius:14px; padding:14px 20px; margin-bottom:20px; color:#059669; font-size:15px;">
                اطلاعات با موفقیت به‌روزرسانی شد.
            </div>
        @endif

        @if(session('status') == 'password-updated')
            <div style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.15); border-radius:14px; padding:14px 20px; margin-bottom:20px; color:#059669; font-size:15px;">
                رمز عبور با موفقیت تغییر کرد.
            </div>
        @endif

        <!-- ===== فرم ویرایش اطلاعات ===== -->
        <div class="form-card">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('patch')

                <div class="form-group">
                    <label for="name">نام کامل</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                @if(auth()->user()->isMaster())
                    <div class="form-group">
                        <label for="email">ایمیل</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">شماره موبایل</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" dir="ltr" required>
                        @error('phone')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                @else
                    <!-- حسابدار فقط نام و رمز عبور را می‌تواند ویرایش کند -->
                    <div class="form-group">
                        <label for="email">ایمیل</label>
                        <input type="email" id="email" value="{{ $user->email }}" disabled style="background:#f1f3f5; cursor:not-allowed; opacity:0.7;">
                        <div class="help-text">تغییر ایمیل فقط توسط مدیر امکان‌پذیر است.</div>
                    </div>

                    <div class="form-group">
                        <label for="phone">شماره موبایل</label>
                        <input type="text" id="phone" value="{{ $user->phone }}" dir="ltr" disabled style="background:#f1f3f5; cursor:not-allowed; opacity:0.7;">
                        <div class="help-text">تغییر شماره موبایل فقط توسط مدیر امکان‌پذیر است.</div>
                    </div>
                @endif

                <div class="form-actions">
                    <a href="{{ route('dashboard') }}" class="btn-cancel">بازگشت</a>
                    <button type="submit" class="btn-submit">ذخیره تغییرات</button>
                </div>
            </form>
        </div>

        <!-- ===== فرم تغییر رمز عبور ===== -->
        <div class="form-card" style="margin-top: 30px;">
            <h3 style="font-size: 18px; font-weight: 600; color: #1a1a2e; margin-bottom: 20px;">تغییر رمز عبور</h3>
            
            <form method="POST" action="{{ route('profile.password.update') }}">
                @csrf
                @method('put')

                <div class="form-group">
                    <label for="current_password">رمز عبور فعلی</label>
                    <input type="password" id="current_password" name="current_password" required>
                    @error('current_password')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">رمز عبور جدید</label>
                    <input type="password" id="password" name="password" placeholder="حداقل ۸ کاراکتر" required>
                    <div class="help-text">حداقل ۸ کاراکتر</div>
                    @error('password')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">تکرار رمز عبور جدید</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">تغییر رمز عبور</button>
                </div>
            </form>
        </div>

    </div>

</body>
</html>