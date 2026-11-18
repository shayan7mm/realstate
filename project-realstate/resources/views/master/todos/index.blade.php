<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لیست کارها</title>
    <link rel="stylesheet" href="{{ asset('css/Style.css') }}">
    <style>
        .todo-item {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.04);
            border-radius: 14px;
            padding: 16px 20px;
            transition: all 0.3s ease;
            margin-bottom: 12px;
        }
        .todo-item:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        }
        .todo-item .title {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a2e;
        }
        .todo-item .title.completed {
            text-decoration: line-through;
            color: #9ca3af;
        }
        .todo-item .description {
            font-size: 14px;
            color: #6b7280;
            margin-top: 4px;
        }
        .todo-item .meta {
            font-size: 13px;
            color: #9ca3af;
            margin-top: 6px;
        }
        .todo-item .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .todo-item .actions a, .todo-item .actions button {
            padding: 4px 14px;
            border-radius: 8px;
            font-size: 12px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
        }
        .todo-item .actions .edit {
            background: rgba(79, 70, 229, 0.08);
            color: #4f46e5;
        }
        .todo-item .actions .edit:hover {
            background: rgba(79, 70, 229, 0.15);
        }
        .todo-item .actions .delete {
            background: rgba(239, 68, 68, 0.06);
            color: #dc2626;
        }
        .todo-item .actions .delete:hover {
            background: rgba(239, 68, 68, 0.12);
        }
        .todo-item .actions .toggle {
            background: rgba(16, 185, 129, 0.08);
            color: #059669;
        }
        .todo-item .actions .toggle:hover {
            background: rgba(16, 185, 129, 0.15);
        }
        .todo-item .actions .toggle.completed {
            background: rgba(245, 158, 11, 0.08);
            color: #d97706;
        }
        .todo-item .actions .toggle.completed:hover {
            background: rgba(245, 158, 11, 0.15);
        }
        .todo-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .todo-stats .item {
            background: #ffffff;
            padding: 16px 20px;
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.04);
            text-align: center;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.03);
        }
        .todo-stats .item .label {
            color: #6b7280;
            font-size: 14px;
        }
        .todo-stats .item .value {
            font-size: 24px;
            font-weight: 700;
        }
        .todo-stats .item .value.pending {
            color: #f59e0b;
        }
        .todo-stats .item .value.completed {
            color: #059669;
        }
        .todo-stats .item .value.total {
            color: #4f46e5;
        }
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
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }
        .empty-state p {
            font-size: 18px;
            margin-bottom: 12px;
        }
        .empty-state a {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 500;
        }
        .empty-state a:hover {
            text-decoration: underline;
        }
        .badge {
            display: inline-block;
            padding: 2px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .badge.completed {
            background: rgba(16, 185, 129, 0.10);
            color: #059669;
        }
        .badge.pending {
            background: rgba(245, 158, 11, 0.10);
            color: #d97706;
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
                    <h1>لیست کارها</h1>
                    <div class="subtitle">مدیریت وظایف روزانه</div>
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
                <a href="{{ route('dashboard') }}" style="color:#6b7280; text-decoration:none; font-size:14px;">داشبورد</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-logout">خروج</button>
                </form>
            </div>
        </header>

        <!-- آمار -->
        <div class="todo-stats">
            <div class="item">
                <div class="label">کل کارها</div>
                <div class="value total">{{ $totalAll }}</div>
            </div>
            <div class="item">
                <div class="label">انجام شده</div>
                <div class="value completed">{{ $totalCompleted }}</div>
            </div>
            <div class="item">
                <div class="label">در انتظار</div>
                <div class="value pending">{{ $totalPending }}</div>
            </div>
            <div class="item" style="display:flex; align-items:center; justify-content:center;">
                <a href="{{ route('master.todos.create') }}" class="quick-btn primary" style="padding:10px 20px; font-size:14px; text-decoration:none;">
                    + کار جدید
                </a>
            </div>
        </div>

        <!-- سرچ و فیلتر -->
        <form method="GET" action="{{ route('master.todos.index') }}" class="search-box">
            <input type="text" name="search" placeholder="جستجو در عنوان یا توضیحات..." value="{{ request('search') }}">
            <select name="filter">
                <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>همه</option>
                <option value="pending" {{ request('filter') == 'pending' ? 'selected' : '' }}>در انتظار</option>
                <option value="completed" {{ request('filter') == 'completed' ? 'selected' : '' }}>انجام شده</option>
            </select>
            <button type="submit">جستجو</button>
            @if(request('search') || request('filter'))
                <a href="{{ route('master.todos.index') }}" style="padding:10px 20px; background:#e5e7eb; border-radius:12px; color:#1a1a2e; text-decoration:none;">حذف فیلتر</a>
            @endif
        </form>

        @if(session('success'))
            <div style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.15); border-radius:14px; padding:14px 20px; margin-bottom:20px; color:#059669; font-size:15px;">
                {{ session('success') }}
            </div>
        @endif

        <!-- لیست کارها -->
        @forelse($todos as $todo)
            <div class="todo-item">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px;">
                    <div style="flex:1;">
                        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                            <span class="title {{ $todo->is_completed ? 'completed' : '' }}">
                                {{ $todo->title }}
                            </span>
                            <span class="badge {{ $todo->is_completed ? 'completed' : 'pending' }}">
                                {{ $todo->is_completed ? 'انجام شده' : 'در انتظار' }}
                            </span>
                        </div>
                        @if($todo->description)
                            <div class="description">{{ $todo->description }}</div>
                        @endif
                    
                        <div class="meta">
                            @if($todo->due_date)
                                تاریخ سررسید: {{ $todo->due_date }}
                            @endif
                            @if($todo->completed_at && $todo->is_completed)
                                <span style="margin-right:12px;">
                                    تاریخ انجام: {{ verta($todo->completed_at)->format('%d %B %Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="actions">
                        <form action="{{ route('master.todos.toggle', $todo) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="toggle {{ $todo->is_completed ? 'completed' : '' }}">
                                {{ $todo->is_completed ? 'بازگشت' : 'انجام شد' }}
                            </button>
                        </form>
                        <a href="{{ route('master.todos.edit', $todo) }}" class="edit">ویرایش</a>
                        <form action="{{ route('master.todos.destroy', $todo) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete" onclick="return confirm('آیا مطمئن هستید؟')">حذف</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <p>هیچ کاری ثبت نشده است</p>
                <a href="{{ route('master.todos.create') }}">ثبت اولین کار →</a>
            </div>
        @endforelse

        <div style="margin-top:24px;">
            {{ $todos->appends(request()->query())->links() }}
        </div>

    </div>

</body>
</html>