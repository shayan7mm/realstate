<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ویرایش منزل</title>
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
        .status-toggle input[type="radio"][value="active"]:checked+label {
            background: rgba(16, 185, 129, 0.10);
            color: #059669;
        }
        .status-toggle input[type="radio"][value="archived"]:checked+label {
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
                    <h1>ویرایش منزل</h1>
                    <div class="subtitle">{{ $house->name }}</div>
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

        <a href="{{ route('houses.index') }}" class="back-link">بازگشت به لیست منازل</a>

        <div class="form-card">
            <form method="POST" action="{{ route('houses.update', $house) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">نام منزل <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $house->name) }}" required>
                    @error('name')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="owner_name">نام صاحب منزل <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="owner_name" name="owner_name" value="{{ old('owner_name', $house->owner_name) }}" required>
                    @error('owner_name')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="owner_phone">شماره تماس صاحب منزل <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="owner_phone" name="owner_phone" value="{{ old('owner_phone', $house->owner_phone) }}" dir="ltr" required>
                    @error('owner_phone')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address">آدرس کامل منزل <span style="color:#dc2626;">*</span></label>
                    <textarea id="address" name="address" rows="3" style="width:100%; padding:14px 18px; background:#f8fafc; border:2px solid #e5e7eb; border-radius:14px; color:#1a1a2e; font-size:15px; outline:none; transition:all 0.3s; resize:vertical; font-family:inherit;" required>{{ old('address', $house->address) }}</textarea>
                    @error('address')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">توضیحات (اختیاری)</label>
                    <textarea id="description" name="description" rows="3" style="width:100%; padding:14px 18px; background:#f8fafc; border:2px solid #e5e7eb; border-radius:14px; color:#1a1a2e; font-size:15px; outline:none; transition:all 0.3s; resize:vertical; font-family:inherit;">{{ old('description', $house->description) }}</textarea>
                    @error('description')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>وضعیت منزل</label>
                    <div class="status-toggle">
                        <input type="radio" id="status_active" name="status" value="active" {{ $house->isActive() ? 'checked' : '' }}>
                        <label for="status_active">فعال</label>
                        <input type="radio" id="status_archived" name="status" value="archived" {{ $house->isArchived() ? 'checked' : '' }}>
                        <label for="status_archived">آرشیو</label>
                    </div>
                    @error('status')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-actions">
                    <a href="{{ route('houses.index') }}" class="btn-cancel">بازگشت</a>
                    <button type="submit" class="btn-submit">ذخیره تغییرات</button>
                </div>
            </form>
        </div>

    </div>

</body>
</html>