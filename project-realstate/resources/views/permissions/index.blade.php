<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت دسترسی‌ها - {{ $user->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/Style.css') }}">
</head>
<body>

    <div class="bg-wrapper"></div>

    <div class="container">

        <header class="header">
            <div class="header-left">
                <div class="logo-box">حس</div>
                <div>
                    <h1>مدیریت دسترسی‌ها</h1>
                    <div class="subtitle">{{ $user->name }}</div>
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
                <a href="{{ route('master.users') }}" style="color:#6b7280; text-decoration:none; font-size:14px;">کاربران</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-logout">خروج</button>
                </form>
            </div>
        </header>

        <a href="{{ route('master.users') }}" class="back-link">← بازگشت به لیست کاربران</a>

        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
            <div>
                <h2 style="font-size:20px; color:#1a1a2e;">دسترسی‌های {{ $user->name }}</h2>
                <p style="color:#6b7280; font-size:14px;">نقش: {{ $user->isMaster() ? 'مدیر ارشد' : 'حسابدار' }}</p>
            </div>
            <a href="{{ route('permissions.edit', $user) }}" class="quick-btn primary" style="padding:12px 24px; font-size:15px; text-decoration:none;">
                ✏️ ویرایش دسترسی‌ها
            </a>
        </div>

        @if(session('success'))
            <div style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.15); border-radius:14px; padding:14px 20px; margin-bottom:20px; color:#059669; font-size:15px;">
                {{ session('success') }}
            </div>
        @endif

        <!-- ===== PERMISSION LIST ===== -->
        <div class="permission-grid">
            @forelse($permissions as $permission)
                @php
                    $moduleName = $modules[$permission->module] ?? $permission->module;
                    $status = 'denied';
                    $statusText = '❌ بدون دسترسی';
                    $statusClass = 'denied';
                    
                    if ($permission->access) {
                        if ($permission->item_ids === null) {
                            $status = 'allowed';
                            $statusText = '✅ دسترسی کامل';
                            $statusClass = 'allowed';
                        } elseif (is_array($permission->item_ids) && count($permission->item_ids) > 0) {
                            $status = 'partial';
                            $statusText = '🔶 دسترسی محدود';
                            $statusClass = 'partial';
                        }
                    }
                @endphp

                <div class="permission-item">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span class="module-name">{{ $moduleName }}</span>
                        <span class="module-status {{ $statusClass }}">{{ $statusText }}</span>
                    </div>

                    @if($permission->item_ids !== null && is_array($permission->item_ids))
                        <div class="items-list">
                            @if(count($permission->item_ids) > 0)
                                @foreach($permission->item_ids as $itemId)
                                    <span class="tag">#{{ $itemId }}</span>
                                @endforeach
                            @else
                                <span class="tag none">هیچ آیتمی</span>
                            @endif
                        </div>
                    @elseif($permission->access && $permission->item_ids === null)
                        <div class="items-list">
                            <span class="tag all">همه آیتم‌ها</span>
                        </div>
                    @endif

                    <div style="margin-top:10px; padding-top:10px; border-top:1px solid #f1f3f5;">
                        <form action="{{ route('permissions.destroy', $permission) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="padding:4px 14px; border-radius:8px; font-size:12px; background:rgba(239,68,68,0.06); color:#dc2626; border:none; cursor:pointer;" onclick="return confirm('آیا مطمئن هستید؟')">
                                حذف دسترسی
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div style="grid-column:1/-1; text-align:center; padding:40px 20px; color:#6b7280;">
                    <p style="font-size:16px;">هیچ دسترسی برای این کاربر تعریف نشده است</p>
                    <a href="{{ route('permissions.edit', $user) }}" style="color:#4f46e5; text-decoration:none; font-weight:500;">
                        تنظیم دسترسی‌ها →
                    </a>
                </div>
            @endforelse
        </div>

    </div>

</body>
</html>