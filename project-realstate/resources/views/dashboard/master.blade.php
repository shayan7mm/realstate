<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داشبورد مدیریت</title>
    <link rel="stylesheet" href="{{ asset('css/Style.css') }}">
    <style>
        .section-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 30px;
        }
        .section-grid .btn-section {
            padding: 18px 20px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            text-align: center;
            color: #fff;
            cursor: pointer;
            border: none;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }
        .section-grid .btn-section:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }
        .section-grid .btn-section .icon {
            display: block;
            font-size: 28px;
            margin-bottom: 6px;
        }
        .section-grid .btn-section.cars { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .section-grid .btn-section.houses { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        .section-grid .btn-section.persons { background: linear-gradient(135deg, #10b981, #059669); }
        .section-grid .btn-section.organizations { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .section-grid .btn-section.expenses { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .section-grid .btn-section.users { background: linear-gradient(135deg, #06b6d4, #0891b2); }
        .section-grid .btn-section.permissions { background: linear-gradient(135deg, #ec4899, #db2777); }
        .section-grid .btn-section.reports { background: linear-gradient(135deg, #6366f1, #4f46e5); }

        .accountant-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 18px 22px;
            border: 1px solid rgba(0, 0, 0, 0.04);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
        }
        .accountant-card:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        }
        .accountant-card .name {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a2e;
        }
        .accountant-card .info {
            font-size: 13px;
            color: #6b7280;
        }
        .accountant-card .status-badge {
            font-size: 12px;
            padding: 2px 12px;
            border-radius: 20px;
        }
        .accountant-card .status-badge.active {
            background: rgba(16, 185, 129, 0.10);
            color: #059669;
        }
        .accountant-card .status-badge.inactive {
            background: rgba(239, 68, 68, 0.06);
            color: #dc2626;
        }
        .accountant-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
            margin-top: 16px;
        }
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 12px;
        }
        .section-title .badge {
            font-size: 13px;
            font-weight: 400;
            color: #6b7280;
        }
    </style>
</head>
<body>

    <div class="bg-wrapper"></div>

    <div class="container">

        <!-- ===== HEADER ===== -->
        <header class="header">
            <div class="header-left">
                <div class="logo-box">حس</div>
                <div>
                    <h1>سیستم حسابداری</h1>
                    <div class="subtitle">پنل مدیریت - مستر</div>
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
                <a href="{{ route('profile.edit') }}" style="color:#6b7280; text-decoration:none; font-size:14px;">پروفایل</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-logout">خروج از حساب</button>
                </form>
            </div>
        </header>

        <!-- ===== STATS ===== -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $totalAccountants }}</div>
                <div class="stat-label">کل حسابداران</div>
               
            </div>

            <div class="stat-card">
                <div class="stat-number">{{ $activeAccountants }}</div>
                <div class="stat-label">حسابداران فعال</div>
                
            </div>

            <div class="stat-card">
                <div class="stat-number">{{ $inactiveAccountants }}</div>
                <div class="stat-label">حسابداران غیرفعال</div>
               
            </div>

            <div class="stat-card">
                <div class="stat-number">{{ $totalAccountants }}</div>
                <div class="stat-label">مدیریت کاربران</div>
                <a href="{{ route('master.users') }}" style="color:#4f46e5; text-decoration:none; font-size:14px; display:inline-block; margin-top:8px; font-weight:500;">
                    مشاهده همه →
                </a>
            </div>
        </div>

        <!-- ===== SECTION BUTTONS (رنگی رنگی) ===== -->
        <div class="section-grid">
            <a href="{{ route('cars.index') }}" class="btn-section cars">
                <span class="icon"></span>
                ماشین‌ها
            </a>
            <a href="{{ route('houses.index') }}" class="btn-section houses">
                <span class="icon"></span>
                منازل
            </a>
            <a href="{{ route('persons.index') }}" class="btn-section persons">
                <span class="icon"></span>
                اشخاص
            </a>
            <a href="{{ route('organizations.index') }}" class="btn-section organizations">
                <span class="icon"></span>
                ارگان‌ها
            </a>
            <a href="{{ route('expenses.index') }}" class="btn-section expenses">
                <span class="icon"></span>
                هزینه‌ها
            </a>
            <a href="{{ route('master.users') }}" class="btn-section users">
                <span class="icon"></span>
                کاربران
            </a>
            <a href="{{ route('master.todos.index') }}" class="btn-section" style="background: linear-gradient(135deg, #f472b6, #ec4899);">
                کار روزانه
            </a>
        </div>

        <!-- ===== RECENT ACCOUNTANTS ===== -->
        <div class="table-wrapper">
            <div class="table-header">
                <h3>📋 آخرین حسابداران ثبت‌نام شده</h3>
                <div class="count">{{ $recentUsers->count() }} نفر</div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>نام</th>
                            <th>ایمیل</th>
                            <th>موبایل</th>
                            <th>وضعیت</th>
                            <th>تاریخ ثبت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentUsers as $key => $user)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td dir="ltr">{{ $user->phone }}</td>
                                <td>
                                    <span class="status-badge {{ $user->is_active ? 'active' : 'inactive' }}">
                                        {{ $user->is_active ? 'فعال' : 'غیرفعال' }}
                                    </span>
                                </td>
                                <td>{{ verta($user->created_at)->format('%d %B %Y') }}</td>
                                <td>
                                    <div class="action-btns">
                                        <a href="{{ route('master.users.edit', $user) }}" class="edit">ویرایش</a>
                                        <a href="{{ route('permissions.index', $user) }}" class="edit" style="background:rgba(236,72,153,0.08); color:#ec4899;">دسترسی</a>
                                        <form action="{{ route('master.users.destroy', $user) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete" onclick="return confirm('آیا از حذف این کاربر مطمئن هستید؟')">حذف</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <p>هیچ حسابداری ثبت نشده است</p>
                                        <a href="{{ route('master.users.create') }}">ثبت اولین حسابدار</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($recentUsers->count() > 0)
                <a href="{{ route('master.users') }}" class="view-all">مشاهده تمام حسابداران →</a>
            @endif
        </div>

    </div>

</body>
</html>