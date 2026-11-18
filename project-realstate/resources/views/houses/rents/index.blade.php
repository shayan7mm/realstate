<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت اجاره - {{ $house->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/Style.css') }}">
    <style>
        .rent-card {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.04);
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 16px;
            transition: all 0.3s ease;
        }
        .rent-card:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        }
        .rent-type {
            display: inline-block;
            padding: 3px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
        }
        .rent-type.rent_out {
            background: rgba(16, 185, 129, 0.10);
            color: #059669;
        }
        .rent-type.rent_in {
            background: rgba(239, 68, 68, 0.06);
            color: #dc2626;
        }
        .rent-status {
            display: inline-block;
            padding: 3px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
        }
        .rent-status.active {
            background: rgba(59, 130, 246, 0.08);
            color: #2563eb;
        }
        .rent-status.completed {
            background: rgba(16, 185, 129, 0.08);
            color: #059669;
        }
        .rent-status.cancelled {
            background: rgba(239, 68, 68, 0.06);
            color: #dc2626;
        }
        .installments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #f1f3f5;
        }
        .installment-item {
            background: #f8fafc;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            text-align: center;
            transition: all 0.3s;
        }
        .installment-item:hover {
            border-color: #4f46e5;
        }
        .installment-item .number {
            font-size: 13px;
            color: #6b7280;
        }
        .installment-item .date {
            font-size: 14px;
            font-weight: 500;
            color: #1a1a2e;
        }
        .installment-item .amount {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a2e;
        }
        .installment-item .status-badge {
            font-size: 11px;
            padding: 2px 10px;
            border-radius: 20px;
        }
        .installment-item .status-badge.paid {
            background: rgba(16, 185, 129, 0.10);
            color: #059669;
        }
        .installment-item .status-badge.unpaid {
            background: rgba(239, 68, 68, 0.06);
            color: #dc2626;
        }
        .rent-summary {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
            margin-top: 8px;
        }
        .rent-summary .item {
            font-size: 14px;
            color: #6b7280;
        }
        .rent-summary .item strong {
            color: #1a1a2e;
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
                    <h1>مدیریت اجاره</h1>
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
                <a href="{{ route('houses.index') }}" style="color:#6b7280; text-decoration:none; font-size:14px;">منازل</a>
                <a href="{{ route('houses.transactions.index', $house) }}" style="color:#6b7280; text-decoration:none; font-size:14px;">کارت مالی</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-logout">خروج</button>
                </form>
            </div>
        </header>

        <a href="{{ route('houses.index') }}" class="back-link">بازگشت به لیست منازل</a>

        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
            <h2 style="font-size:20px; color:#1a1a2e;">اجاره‌های ثبت شده</h2>
            <a href="{{ route('houses.rents.create', $house) }}" class="quick-btn primary" style="padding:12px 24px; font-size:15px; text-decoration:none;">
                + اجاره جدید
            </a>
        </div>

        @if(session('success'))
            <div style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.15); border-radius:14px; padding:14px 20px; margin-bottom:20px; color:#059669; font-size:15px;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background:rgba(239,68,68,0.06); border:1px solid rgba(239,68,68,0.12); border-radius:14px; padding:14px 20px; margin-bottom:20px; color:#dc2626; font-size:15px;">
                {{ session('error') }}
            </div>
        @endif

        <!-- لیست اجاره‌ها -->
        @forelse($rents as $rent)
            <div class="rent-card">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px;">
                    <div>
                        <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                            <span class="rent-type {{ $rent->type }}">
                                {{ $rent->isRentOut() ? '🏠 اجاره داده شده' : '🏠 اجاره گرفته شده' }}
                            </span>
                            <span class="rent-status {{ $rent->status }}">
                                {{ $rent->isActive() ? 'فعال' : ($rent->isCompleted() ? 'تکمیل شده' : 'لغو شده') }}
                            </span>
                        </div>
                        <div style="margin-top:6px;">
                            <span style="font-size:18px; font-weight:600; color:#1a1a2e;">{{ number_format($rent->monthly_amount) }} تومان</span>
                            <span style="color:#6b7280; font-size:14px;">/ ماهانه</span>
                        </div>
                        <div style="color:#6b7280; font-size:14px; margin-top:2px;">
                            از: {{ $rent->start_date }} تا: {{ $rent->end_date }}
                            ({{ $rent->total_months }} ماه)
                        </div>
                        <div class="rent-summary">
                            <div class="item">تعداد اقساط: <strong>{{ $rent->installments->count() }}</strong></div>
                            <div class="item">پرداخت شده: <strong style="color:#059669;">{{ $rent->getPaidCount() }}</strong></div>
                            <div class="item">پرداخت نشده: <strong style="color:#dc2626;">{{ $rent->getUnpaidCount() }}</strong></div>
                            <div class="item">مبلغ پرداختی: <strong>{{ number_format($rent->getTotalPaidAmount()) }}</strong></div>
                        </div>
                    </div>
                    <div style="display:flex; gap:8px;">
                        <a href="{{ route('houses.rents.edit', [$house, $rent]) }}" class="edit" style="padding:4px 14px; border-radius:8px; font-size:12px; background:rgba(79,70,229,0.08); color:#4f46e5; text-decoration:none;">ویرایش</a>
                        <form action="{{ route('houses.rents.destroy', [$house, $rent]) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete" style="padding:4px 14px; border-radius:8px; font-size:12px; background:rgba(239,68,68,0.06); color:#dc2626; border:none; cursor:pointer;" onclick="return confirm('آیا مطمئن هستید؟')">حذف</button>
                        </form>
                    </div>
                </div>

                <!-- اقساط -->
                <div class="installments-grid">
                    @foreach($rent->installments as $installment)
                        <div class="installment-item">
                            <div class="number">قسط {{ $installment->installment_number }}</div>
                            <div class="date">{{ $installment->due_date }}</div>
                            <div class="amount">{{ number_format($installment->amount) }}</div>
                            <div style="margin-top:4px;">
                                @if($installment->is_paid)
                                    <span class="status-badge paid">پرداخت شده</span>
                                    <div style="font-size:11px; color:#6b7280; margin-top:2px;">
                                        {{ $installment->paid_date }}
                                    </div>
                                @else
                                    <span class="status-badge unpaid">پرداخت نشده</span>
                                    <form action="{{ route('houses.rents.pay-installment', [$house, $rent, $installment]) }}" method="POST" style="margin-top:4px;">
                                        @csrf
                                        <input type="hidden" name="paid_date" value="{{ verta()->format('Y/m/d') }}">
                                        <button type="submit" style="padding:2px 10px; border-radius:6px; font-size:11px; background:#4f46e5; color:#fff; border:none; cursor:pointer; width:100%;">
                                            پرداخت
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div style="text-align:center; padding:60px 20px; color:#6b7280;">
                <p style="font-size:18px; margin-bottom:12px;">هیچ اجاره‌ای برای این منزل ثبت نشده است</p>
                <a href="{{ route('houses.rents.create', $house) }}" style="color:#4f46e5; text-decoration:none; font-weight:500;">
                    ثبت اجاره جدید →
                </a>
            </div>
        @endforelse

        <div style="margin-top:24px;">
            {{ $rents->links() }}
        </div>

    </div>

</body>
</html>