<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ویرایش کار</title>
    <link rel="stylesheet" href="{{ asset('css/Style.css') }}">
</head>
<body>

    <div class="bg-wrapper"></div>

    <div class="container">

        <header class="header">
            <div class="header-left">
                <div class="logo-box">حس</div>
                <div>
                    <h1>ویرایش کار</h1>
                    <div class="subtitle">{{ $todo->title }}</div>
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
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-logout">خروج</button>
                </form>
            </div>
        </header>

        <a href="{{ route('master.todos.index') }}" class="back-link">بازگشت به لیست کارها</a>

        @if ($errors->any())
            <div style="background:rgba(239,68,68,0.06); border:1px solid rgba(239,68,68,0.12); border-radius:14px; padding:14px 20px; margin-bottom:20px; color:#dc2626; font-size:15px;">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <div class="form-card">
            <form method="POST" action="{{ route('master.todos.update', $todo) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="title">عنوان کار <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title', $todo->title) }}" required>
                    @error('title')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">توضیحات (اختیاری)</label>
                    <textarea id="description" name="description" rows="3" style="width:100%; padding:14px 18px; background:#f8fafc; border:2px solid #e5e7eb; border-radius:14px; color:#1a1a2e; font-size:15px; outline:none; transition:all 0.3s; resize:vertical; font-family:inherit;">{{ old('description', $todo->description) }}</textarea>
                    @error('description')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="due_date">تاریخ سررسید (اختیاری)</label>
                    <input type="text" 
                           id="due_date" 
                           name="due_date" 
                           value="{{ old('due_date', $todo->due_date) }}"
                           placeholder="مثال: 1404/06/25"
                           dir="ltr"
                           style="width:100%; padding:14px 18px; background:#f8fafc; border:2px solid #e5e7eb; border-radius:14px; color:#1a1a2e; font-size:15px; transition:all 0.3s; outline:none; direction:ltr; text-align:center; font-family:inherit;">
                    <div class="help-text">اختیاری - هر تاریخی که دوست دارید وارد کنید</div>
                    @error('due_date')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-actions">
                    <a href="{{ route('master.todos.index') }}" class="btn-cancel">بازگشت</a>
                    <button type="submit" class="btn-submit">ذخیره تغییرات</button>
                </div>
            </form>
        </div>

    </div>

</body>
</html>