<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت حسابداران</title>
    <link rel="stylesheet" href="{{ asset('css/Style.css') }}">
    <style>
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 24px;
            flex-wrap: wrap;
        }

        .pagination a, .pagination span {
            padding: 8px 18px;
            border-radius: 10px;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            color: #1a1a2e;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 14px;
        }

        .pagination a:hover {
            background: #4f46e5;
            color: #fff;
            border-color: #4f46e5;
        }

        .pagination .active span {
            background: #4f46e5;
            color: #fff;
            border-color: #4f46e5;
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
                    <h1>مدیریت حسابداران</h1>
                    <div class="subtitle">لیست کامل کاربران</div>
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

        <a href="{{ route('dashboard') }}" class="back-link">بازگشت به داشبورد</a>

        <div class="stats-grid" style="margin-bottom:30px;">
            <div class="stat-card">
                <div class="stat-number">{{ $users->total() }}</div>
                <div class="stat-label">کل حسابداران</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $users->where('is_active', true)->count() }}</div>
                <div class="stat-label">فعال</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $users->where('is_active', false)->count() }}</div>
                <div class="stat-label">غیرفعال</div>
            </div>
            <div class="stat-card" style="display:flex; align-items:center; justify-content:center;">
                <a href="{{ route('master.users.create') }}" style="color:#4f46e5; text-decoration:none; font-weight:600; font-size:17px;">
                    ثبت‌نام جدید
                </a>
            </div>
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

        <div class="table-wrapper">
            <div class="table-header">
                <h3>لیست حسابداران</h3>
                <div class="count">{{ $users->total() }} نفر</div>
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
                        @forelse($users as $key => $user)
                            <tr>
                                <td>{{ $users->firstItem() + $key }}</td>
                                <td><strong>{{ $user->name }}</strong></td>
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
                                        <form action="{{ route('master.users.destroy', $user) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete" onclick="return confirm('آیا از حذف {{ $user->name }} مطمئن هستید؟')">حذف</button>
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

            @if($users->hasPages())
                <div class="pagination">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

    </div>

</body>
</html>