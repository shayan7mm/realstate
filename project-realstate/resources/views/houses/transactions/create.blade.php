<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تراکنش جدید - {{ $house->name }}</title>
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
        .type-toggle input[type="radio"][value="income"]:checked+label {
            background: rgba(16, 185, 129, 0.10);
            color: #059669;
        }
        .type-toggle input[type="radio"][value="expense"]:checked+label {
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
                    <h1>تراکنش جدید</h1>
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

        <a href="{{ route('houses.transactions.index', $house) }}" class="back-link">بازگشت به کارت مالی</a>

        <div class="form-card">
            <form method="POST" action="{{ route('houses.transactions.store', $house) }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label>نوع تراکنش <span style="color:#dc2626;">*</span></label>
                    <div class="type-toggle">
                        <input type="radio" id="type_income" name="type" value="income" {{ old('type') == 'income' ? 'checked' : '' }}>
                        <label for="type_income">ورودی</label>
                        <input type="radio" id="type_expense" name="type" value="expense" {{ old('type') == 'expense' ? 'checked' : '' }}>
                        <label for="type_expense">خروجی</label>
                    </div>
                    @error('type')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="amount">مبلغ (تومان) <span style="color:#dc2626;">*</span></label>
                    <input type="number" id="amount" name="amount" value="{{ old('amount') }}" placeholder="مبلغ را وارد کنید" required>
                    @error('amount')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="transaction_date">تاریخ تراکنش <span style="color:#dc2626;">*</span></label>
                    <?php
                        use Hekmatinasser\Verta\Verta;
                        $today = verta()->format('Y/m/d');
                        $oldDate = old('transaction_date') ? old('transaction_date') : $today;
                    ?>
                    <input type="text" 
                           id="transaction_date" 
                           name="transaction_date" 
                           value="{{ $oldDate }}"
                           placeholder="1404/06/25"
                           dir="ltr"
                           required
                           style="width:100%; padding:14px 18px; background:#f8fafc; border:2px solid #e5e7eb; border-radius:14px; color:#1a1a2e; font-size:15px; transition:all 0.3s; outline:none; direction:ltr; text-align:center; font-family:inherit;">
                    <div class="help-text">فرمت: 1404/06/25</div>
                    @error('transaction_date')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="from_account">شماره کارت مبدأ (اختیاری)</label>
                    <input type="text" id="from_account" name="from_account" value="{{ old('from_account') }}" placeholder="مثال: 6037-9910-0500-1234" dir="ltr">
                    @error('from_account')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="to_account">شماره کارت مقصد (اختیاری)</label>
                    <input type="text" id="to_account" name="to_account" value="{{ old('to_account') }}" placeholder="مثال: 6037-9910-0500-5678" dir="ltr">
                    @error('to_account')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="receipt_image">تصویر فیش واریزی (اختیاری)</label>
                    <input type="file" id="receipt_image" name="receipt_image" accept="image/*">
                    <div class="help-text">حداکثر حجم: ۲ مگابایت</div>
                    @error('receipt_image')
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
                    <a href="{{ route('houses.transactions.index', $house) }}" class="btn-cancel">بازگشت</a>
                    <button type="submit" class="btn-submit">ثبت تراکنش</button>
                </div>
            </form>
        </div>

    </div>

</body>
</html>