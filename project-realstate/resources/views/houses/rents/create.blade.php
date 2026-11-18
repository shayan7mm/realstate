<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ثبت اجاره جدید - {{ $house->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/Style.css') }}">
    <style>
        .type-toggle {
            display: flex;
            gap: 12px;
            background: #f3f4f6;
            padding: 6px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }
        .type-toggle label {
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
        .type-toggle input[type="radio"] {
            display: none;
        }
        .type-toggle input[type="radio"][value="rent_out"]:checked+label {
            background: rgba(16, 185, 129, 0.10);
            color: #059669;
        }
        .type-toggle input[type="radio"][value="rent_in"]:checked+label {
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
                    <h1>ثبت اجاره جدید</h1>
                    <div class="subtitle">{{ $house->name }} - {{ $house->owner_name }}</div>
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

        <a href="{{ route('houses.rents.index', $house) }}" class="back-link">بازگشت به لیست اجاره‌ها</a>

        <div class="form-card">
            <form method="POST" action="{{ route('houses.rents.store', $house) }}">
                @csrf

                <div class="form-group">
                    <label>نوع اجاره <span style="color:#dc2626;">*</span></label>
                    <div class="type-toggle">
                        <input type="radio" id="type_rent_out" name="type" value="rent_out" {{ old('type') == 'rent_out' ? 'checked' : '' }}>
                        <label for="type_rent_out">اجاره داده شده (ورودی)</label>
                        <input type="radio" id="type_rent_in" name="type" value="rent_in" {{ old('type') == 'rent_in' ? 'checked' : '' }}>
                        <label for="type_rent_in">اجاره گرفته شده (خروجی)</label>
                    </div>
                    @error('type')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="monthly_amount">مبلغ اجاره ماهیانه (تومان) <span style="color:#dc2626;">*</span></label>
                    <input type="number" id="monthly_amount" name="monthly_amount" value="{{ old('monthly_amount') }}" placeholder="مبلغ را وارد کنید" required>
                    @error('monthly_amount')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="start_date">تاریخ شروع اجاره <span style="color:#dc2626;">*</span></label>
                    <?php
                        use Hekmatinasser\Verta\Verta;
                        $today = verta()->format('Y/m/d');
                        $oldDate = old('start_date') ? old('start_date') : $today;
                    ?>
                    <input type="text" 
                           id="start_date" 
                           name="start_date" 
                           value="{{ $oldDate }}"
                           placeholder="1404/06/25"
                           dir="ltr"
                           required
                           style="width:100%; padding:14px 18px; background:#f8fafc; border:2px solid #e5e7eb; border-radius:14px; color:#1a1a2e; font-size:15px; transition:all 0.3s; outline:none; direction:ltr; text-align:center; font-family:inherit;">
                    <div class="help-text">فرمت: 1404/06/25</div>
                    @error('start_date')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="total_months">تعداد ماه‌های اجاره <span style="color:#dc2626;">*</span></label>
                    <input type="number" id="total_months" name="total_months" value="{{ old('total_months', 12) }}" min="1" max="60" required>
                    <div class="help-text">حداکثر ۶۰ ماه</div>
                    @error('total_months')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-actions">
                    <a href="{{ route('houses.rents.index', $house) }}" class="btn-cancel">بازگشت</a>
                    <button type="submit" class="btn-submit">ثبت اجاره</button>
                </div>
            </form>
        </div>

    </div>

</body>
</html>