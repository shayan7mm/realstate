<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ویرایش حسابدار</title>
    <link rel="stylesheet" href="{{ asset('css/Style.css') }}">
    <style>
        .status-toggle {
            display: flex;
            gap: 12px;
            background: #f3f4f6;
            padding: 6px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .status-toggle label {
            flex: 1;
            text-align: center;
            padding: 10px 16px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 15px;
            color: #6b7280;
            font-weight: 500;
        }

        .status-toggle input[type="radio"] {
            display: none;
        }

        .status-toggle input[type="radio"][value="1"]:checked + label {
            background: rgba(16, 185, 129, 0.10);
            color: #059669;
        }

        .status-toggle input[type="radio"][value="0"]:checked + label {
            background: rgba(239, 68, 68, 0.06);
            color: #dc2626;
        }
    </style>
</head>
<body>

    <div class="bg-wrapper"></div>

    <div class="container">

        <header class="header">
            <div class="header-left">
                <div class="logo-box">حس</div>
                <div>
                    <h1>ویرایش حسابدار</h1>
                    <div class="subtitle">{{ $user->name }}</div>
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
            <form method="POST" action="{{ route('master.users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">نام کامل</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

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

                <div class="form-group">
                    <label>وضعیت حساب</label>
                    <div class="status-toggle">
                        <input type="radio" id="active_yes" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}>
                        <label for="active_yes">فعال</label>
                        <input type="radio" id="active_no" name="is_active" value="0" {{ !$user->is_active ? 'checked' : '' }}>
                        <label for="active_no">غیرفعال</label>
                    </div>
                    @error('is_active')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">رمز عبور جدید <span style="color:#6b7280; font-weight:400;">(اختیاری)</span></label>
                    <input type="password" id="password" name="password" placeholder="برای تغییر رمز عبور پر کنید">
                    <div class="help-text">برای تغییر رمز عبور، این بخش را پر کنید. در غیر این صورت خالی بگذارید.</div>
                    @error('password')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">تکرار رمز عبور جدید</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="تکرار رمز عبور جدید">
                </div>

                <div class="form-actions">
                    <a href="{{ route('master.users') }}" class="btn-cancel">بازگشت</a>
                    <button type="submit" class="btn-submit">ذخیره تغییرات</button>
                </div>
            </form>
        </div>

    </div>

</body>
</html>