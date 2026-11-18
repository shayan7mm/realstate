<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت اشخاص</title>
    <link rel="stylesheet" href="{{ asset('css/Style.css') }}">
    <style>
        .person-card {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.04);
            border-radius: 18px;
            padding: 24px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.03);
        }
        .person-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.06);
        }
        .person-card .person-name {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a2e;
        }
        .person-stats {
            display: flex;
            gap: 20px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #f1f3f5;
        }
        .person-stats .stat-item {
            font-size: 14px;
        }
        .person-stats .stat-item .label {
            color: #6b7280;
        }
        .person-stats .stat-item .value {
            font-weight: 600;
        }
        .person-stats .stat-item .value.income { color: #059669; }
        .person-stats .stat-item .value.expense { color: #dc2626; }
        .person-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .search-box {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }
        .search-box input {
            flex: 1;
            padding: 12px 18px;
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            font-size: 15px;
            outline: none;
            transition: all 0.3s;
            min-width: 200px;
        }
        .search-box input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.06);
        }
        .search-box select {
            padding: 12px 18px;
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            font-size: 15px;
            outline: none;
            background: #fff;
            min-width: 150px;
        }
        .search-box select:focus {
            border-color: #4f46e5;
        }
        .search-box button {
            padding: 12px 28px;
            background: #4f46e5;
            color: #fff;
            border: none;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        .search-box button:hover {
            background: #7c3aed;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-badge.active {
            background: rgba(16, 185, 129, 0.10);
            color: #059669;
        }
        .status-badge.archived {
            background: rgba(239, 68, 68, 0.08);
            color: #dc2626;
        }
        .person-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .person-actions a, .person-actions button {
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 12px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
        }
        .person-actions .edit { background: rgba(79,70,229,0.08); color: #4f46e5; }
        .person-actions .edit:hover { background: rgba(79,70,229,0.15); }
        .person-actions .delete { background: rgba(239,68,68,0.06); color: #dc2626; }
        .person-actions .delete:hover { background: rgba(239,68,68,0.12); }
        .person-actions .restore { background: rgba(16,185,129,0.08); color: #059669; }
        .person-actions .restore:hover { background: rgba(16,185,129,0.15); }
    </style>
</head>
<body>

    <div class="bg-wrapper"></div>

    <div class="container">

        <header class="header">
            <div class="header-left">
                <div class="logo-box">حس</div>
                <div>
                    <h1>مدیریت اشخاص</h1>
                    <div class="subtitle">سیستم حسابداری</div>
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
                <a href="{{ route('dashboard') }}" style="color:#6b7280; text-decoration:none; font-size:14px;">داشبورد</a>
                <a href="{{ route('cars.index') }}" style="color:#6b7280; text-decoration:none; font-size:14px;">ماشین‌ها</a>
                <a href="{{ route('houses.index') }}" style="color:#6b7280; text-decoration:none; font-size:14px;">منازل</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-logout">خروج</button>
                </form>
            </div>
        </header>

        <!-- سرچ و دکمه افزودن -->
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
            <form method="GET" action="{{ route('persons.index') }}" class="search-box" style="margin-bottom:0; flex:1;">
                <input type="text" name="search" placeholder="جستجوی نام شخص..." value="{{ request('search') }}">
                <select name="status">
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>فعال</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>آرشیو</option>
                    <option value="" {{ !request('status') ? 'selected' : '' }}>همه</option>
                </select>
                <button type="submit">جستجو</button>
                @if(request('search') || request('status'))
                    <a href="{{ route('persons.index') }}" style="padding:12px 20px; background:#e5e7eb; border-radius:14px; color:#1a1a2e; text-decoration:none;">حذف فیلتر</a>
                @endif
            </form>
            <a href="{{ route('persons.create') }}" class="quick-btn primary" style="padding:12px 24px; font-size:15px; text-decoration:none;">
                + شخص جدید
            </a>
        </div>

        @if(session('success'))
            <div style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.15); border-radius:14px; padding:14px 20px; margin-bottom:20px; color:#059669; font-size:15px;">
                {{ session('success') }}
            </div>
        @endif

        <!-- لیست اشخاص -->
        <div class="person-grid">
            @forelse($persons as $person)
                @php
                    $totalIncome = $person->totalIncome();
                    $totalExpense = $person->totalExpense();
                    $profit = $person->profit();
                @endphp

                <div class="person-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div class="person-name">{{ $person->name }}</div>
                            @if($person->description)
                                <div style="color:#6b7280; font-size:14px; margin-top:4px;">{{ $person->description }}</div>
                            @endif
                            <div style="margin-top:6px;">
                                <span class="status-badge {{ $person->status }}">
                                    {{ $person->isActive() ? 'فعال' : 'آرشیو' }}
                                </span>
                            </div>
                        </div>
                        <div class="person-actions">
                            @if($person->isArchived())
                                <form action="{{ route('persons.restore', $person->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="restore">بازیابی</button>
                                </form>
                            @endif
                            <a href="{{ route('persons.edit', $person) }}" class="edit">ویرایش</a>
                            <form action="{{ route('persons.destroy', $person) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete" onclick="return confirm('آیا مطمئن هستید؟')">
                                    {{ $person->isActive() ? 'آرشیو' : 'حذف' }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="person-stats">
                        <div class="stat-item">
                            <span class="label">ورودی:</span>
                            <span class="value income">{{ number_format($totalIncome) }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="label">خروجی:</span>
                            <span class="value expense">{{ number_format($totalExpense) }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="label">سود:</span>
                            <span class="value" style="color:{{ $profit >= 0 ? '#059669' : '#dc2626' }};">
                                {{ number_format($profit) }}
                            </span>
                        </div>
                    </div>

                    <div style="margin-top:14px; padding-top:14px; border-top:1px solid #f1f3f5; display:flex; gap:12px;">
                        <a href="{{ route('persons.transactions.index', $person) }}" class="quick-btn" style="flex:1; padding:10px; font-size:14px; background:rgba(79,70,229,0.06); color:#4f46e5; text-decoration:none; text-align:center; border-radius:12px;">
                            📊 کارت مالی
                        </a>
                        <a href="{{ route('persons.transactions.create', $person) }}" class="quick-btn" style="flex:1; padding:10px; font-size:14px; background:rgba(16,185,129,0.06); color:#059669; text-decoration:none; text-align:center; border-radius:12px;">
                            + تراکنش جدید
                        </a>
                    </div>
                </div>
            @empty
                <div style="grid-column:1/-1; text-align:center; padding:60px 20px; color:#6b7280;">
                    <p style="font-size:18px; margin-bottom:12px;">هیچ شخصی ثبت نشده است</p>
                    <a href="{{ route('persons.create') }}" style="color:#4f46e5; text-decoration:none; font-weight:500;">ثبت اولین شخص →</a>
                </div>
            @endforelse
        </div>

        <div style="margin-top:24px;">
            {{ $persons->appends(request()->query())->links() }}
        </div>

    </div>

</body>
</html>