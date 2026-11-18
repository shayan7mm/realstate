<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>کارت مالی - {{ $car->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/Style.css') }}">
    <style>
        .transaction-item {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.04);
            border-radius: 14px;
            padding: 16px 20px;
            transition: all 0.3s ease;
            margin-bottom: 12px;
        }
        .transaction-item:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        }
        .transaction-item .amount {
            font-size: 18px;
            font-weight: 700;
        }
        .transaction-item .amount.income { color: #059669; }
        .transaction-item .amount.expense { color: #dc2626; }
        .transaction-item .date { color: #6b7280; font-size: 14px; }
        .transaction-item .accounts { color: #6b7280; font-size: 13px; }
        .transaction-item .description { color: #1a1a2e; font-size: 14px; margin-top: 4px; }
        .transaction-item .receipt-img {
            max-width: 100px;
            max-height: 100px;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 8px;
        }
        .summary-box {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .summary-box .item {
            background: #ffffff;
            padding: 16px 20px;
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.04);
            text-align: center;
        }
        .summary-box .item .label { color: #6b7280; font-size: 14px; }
        .summary-box .item .value { font-size: 22px; font-weight: 700; }
        .summary-box .item .value.income { color: #059669; }
        .summary-box .item .value.expense { color: #dc2626; }
        .search-box {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }
        .search-box input, .search-box select {
            padding: 10px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            outline: none;
            transition: all 0.3s;
            background: #fff;
        }
        .search-box input:focus, .search-box select:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.06);
        }
        .search-box button {
            padding: 10px 24px;
            background: #4f46e5;
            color: #fff;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .search-box button:hover {
            background: #7c3aed;
        }
        .type-badge {
            display: inline-block;
            padding: 2px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .type-badge.income {
            background: rgba(16, 185, 129, 0.10);
            color: #059669;
        }
        .type-badge.expense {
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
                    <h1>کارت مالی</h1>
                    <div class="subtitle">{{ $car->name }} - {{ $car->owner_name }}</div>
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
                <a href="{{ route('cars.index') }}" style="color:#6b7280; text-decoration:none; font-size:14px;">ماشین‌ها</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-logout">خروج</button>
                </form>
            </div>
        </header>

        <a href="{{ route('cars.index') }}" class="back-link">بازگشت به لیست ماشین‌ها</a>

        <!-- خلاصه مالی -->
        <div class="summary-box">
            <div class="item">
                <div class="label">جمع ورودی‌ها</div>
                <div class="value income">{{ number_format($totalIncome) }}</div>
            </div>
            <div class="item">
                <div class="label">جمع خروجی‌ها</div>
                <div class="value expense">{{ number_format($totalExpense) }}</div>
            </div>
            <div class="item">
                <div class="label">سود خالص</div>
                <div class="value" style="color:{{ $profit >= 0 ? '#059669' : '#dc2626' }};">
                    {{ number_format($profit) }}
                </div>
            </div>
            <div class="item">
                <div class="label">تعداد تراکنش‌ها</div>
                <div class="value" style="color:#4f46e5;">{{ $transactions->total() }}</div>
            </div>
        </div>

        <!-- دکمه تراکنش جدید -->
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
            <a href="{{ route('cars.transactions.create', $car) }}" class="quick-btn primary" style="padding:12px 24px; font-size:15px; text-decoration:none;">
                + تراکنش جدید
            </a>
        </div>

        <!-- سرچ -->
        <form method="GET" action="{{ route('cars.transactions.index', $car) }}" class="search-box">
            <input type="text" name="search" placeholder="جستجو در تراکنش‌ها..." value="{{ request('search') }}">
            <select name="type">
                <option value="">همه</option>
                <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>ورودی</option>
                <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>خروجی</option>
            </select>
            <button type="submit">جستجو</button>
            @if(request('search') || request('type'))
                <a href="{{ route('cars.transactions.index', $car) }}" style="padding:10px 20px; background:#e5e7eb; border-radius:12px; color:#1a1a2e; text-decoration:none;">حذف فیلتر</a>
            @endif
        </form>

        @if(session('success'))
            <div style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.15); border-radius:14px; padding:14px 20px; margin-bottom:20px; color:#059669; font-size:15px;">
                {{ session('success') }}
            </div>
        @endif

        <!-- لیست تراکنش‌ها -->
        @forelse($transactions as $transaction)
            <div class="transaction-item">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:8px;">
                    <div>
                        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                            <span class="type-badge {{ $transaction->type }}">
                                {{ $transaction->isIncome() ? 'ورودی' : 'خروجی' }}
                            </span>
                            <span class="date">
                                <?php
                                    try {
                                        $date = \Hekmatinasser\Verta\Verta::parseFormat('Y/m/d', $transaction->transaction_date)->format('%d %B %Y');
                                    } catch (\Exception $e) {
                                        $date = $transaction->transaction_date;
                                    }
                                    echo $date;
                                ?>
                            </span>
                        </div>
                        <div class="amount {{ $transaction->type }}">
                            {{ number_format($transaction->amount) }} تومان
                        </div>
                        @if($transaction->from_account || $transaction->to_account)
                            <div class="accounts">
                                از: {{ $transaction->from_account ?? '-' }} 
                                به: {{ $transaction->to_account ?? '-' }}
                            </div>
                        @endif
                        @if($transaction->description)
                            <div class="description">{{ $transaction->description }}</div>
                        @endif
                    </div>
                    <div style="display:flex; gap:8px; align-items:center;">
                        @if($transaction->receipt_image)
                            <img src="{{ asset($transaction->receipt_image) }}" 
                                alt="فیش" 
                                class="receipt-img"
                                onclick="window.open(this.src, '_blank')">
                        @endif
                        <div style="display:flex; gap:4px;">
                            <a href="{{ route('cars.transactions.edit', [$car, $transaction]) }}" 
                               style="padding:4px 12px; border-radius:8px; font-size:12px; background:rgba(79,70,229,0.08); color:#4f46e5; text-decoration:none;">
                                ویرایش
                            </a>
                            <form action="{{ route('cars.transactions.destroy', [$car, $transaction]) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="padding:4px 12px; border-radius:8px; font-size:12px; background:rgba(239,68,68,0.06); color:#dc2626; border:none; cursor:pointer;" onclick="return confirm('آیا مطمئن هستید؟')">
                                    حذف
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div style="text-align:center; padding:60px 20px; color:#6b7280;">
                <p style="font-size:18px; margin-bottom:12px;">هیچ تراکنشی برای این ماشین ثبت نشده است</p>
                <a href="{{ route('cars.transactions.create', $car) }}" style="color:#4f46e5; text-decoration:none; font-weight:500;">
                    ثبت اولین تراکنش →
                </a>
            </div>
        @endforelse

        <div style="margin-top:24px;">
            {{ $transactions->appends(request()->query())->links() }}
        </div>

    </div>

</body>
</html>