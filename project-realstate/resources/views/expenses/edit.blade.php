<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ویرایش - {{ $expense->title }}</title>
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
        .current-receipt {
            margin-top: 8px;
            padding: 10px;
            background: #f8fafc;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .current-receipt img {
            max-width: 80px;
            max-height: 80px;
            border-radius: 8px;
        }
        .current-receipt a {
            color: #4f46e5;
            text-decoration: none;
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
                    <h1>ویرایش</h1>
                    <div class="subtitle">{{ $expense->title }}</div>
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

        <a href="{{ route('expenses.index') }}" class="back-link">بازگشت به لیست</a>

        <div class="form-card">
            <form method="POST" action="{{ route('expenses.update', $expense) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="title">عنوان <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title', $expense->title) }}" required>
                    @error('title')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>نوع <span style="color:#dc2626;">*</span></label>
                    <div class="type-toggle">
                        <input type="radio" id="type_income" name="type" value="income" {{ $expense->isIncome() ? 'checked' : '' }}>
                        <label for="type_income">💰 درآمد</label>
                        <input type="radio" id="type_expense" name="type" value="expense" {{ $expense->isExpense() ? 'checked' : '' }}>
                        <label for="type_expense">💸 هزینه</label>
                    </div>
                    @error('type')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="amount">مبلغ (تومان) <span style="color:#dc2626;">*</span></label>
                    <input type="number" id="amount" name="amount" value="{{ old('amount', $expense->amount) }}" required>
                    @error('amount')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="transaction_date">تاریخ <span style="color:#dc2626;">*</span></label>
                    <?php
                        use Hekmatinasser\Verta\Verta;
                        $oldDate = old('transaction_date') ? old('transaction_date') : $expense->transaction_date;
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
                    <input type="text" id="from_account" name="from_account" value="{{ old('from_account', $expense->from_account) }}" dir="ltr">
                    @error('from_account')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="to_account">شماره کارت مقصد (اختیاری)</label>
                    <input type="text" id="to_account" name="to_account" value="{{ old('to_account', $expense->to_account) }}" dir="ltr">
                    @error('to_account')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="receipt_image">تصویر فیش (اختیاری)</label>
                    
                    @if($expense->receipt_image)
                        <div class="current-receipt">
                            <img src="{{ asset($expense->receipt_image) }}" alt="فیش فعلی">
                            <span style="font-size:14px; color:#6b7280;">فیش فعلی</span>
                            <a href="{{ asset($expense->receipt_image) }}" target="_blank" style="font-size:13px;">مشاهده</a>
                        </div>
                    @endif
                    
                    <input type="file" id="receipt_image" name="receipt_image" accept="image/*" style="margin-top:8px;">
                    <div class="help-text">حداکثر حجم: ۲ مگابایت - برای تغییر تصویر جدید آپلود کنید</div>
                    @error('receipt_image')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">توضیحات (اختیاری)</label>
                    <textarea id="description" name="description" rows="3" style="width:100%; padding:14px 18px; background:#f8fafc; border:2px solid #e5e7eb; border-radius:14px; color:#1a1a2e; font-size:15px; outline:none; transition:all 0.3s; resize:vertical; font-family:inherit;">{{ old('description', $expense->description) }}</textarea>
                    @error('description')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-actions">
                    <a href="{{ route('expenses.index') }}" class="btn-cancel">بازگشت</a>
                    <button type="submit" class="btn-submit">ذخیره تغییرات</button>
                </div>
            </form>
        </div>

    </div>

</body>
</html>