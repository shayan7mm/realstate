<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داشبورد حسابدار</title>
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
            color: #ffffff;
            cursor: pointer;
            border: none;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }
        .section-grid .btn-section:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }
        .section-grid .btn-section.cars { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .section-grid .btn-section.houses { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        .section-grid .btn-section.persons { background: linear-gradient(135deg, #10b981, #059669); }
        .section-grid .btn-section.organizations { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .section-grid .btn-section.expenses { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .section-grid .btn-section.profile { background: linear-gradient(135deg, #6366f1, #4f46e5); }
        .section-grid .btn-section.reports { background: linear-gradient(135deg, #06b6d4, #0891b2); }

        .access-denied {
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid rgba(0, 0, 0, 0.04);
        }
        .access-denied p {
            font-size: 16px;
            margin-bottom: 8px;
        }
        .access-denied .icon-big {
            font-size: 48px;
            display: block;
            margin-bottom: 12px;
        }
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 12px;
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
                    <div class="subtitle">پنل حسابدار</div>
                </div>
            </div>
            <div class="header-right">
                <div class="user-badge">
                    <div class="avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    <div class="user-meta">
                        <div class="name">{{ auth()->user()->name }}</div>
                        <div class="role">حسابدار</div>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" style="color:#6b7280; text-decoration:none; font-size:14px; padding:6px 14px; border-radius:8px; background:rgba(0,0,0,0.03);">ویرایش پروفایل</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-logout">خروج از حساب</button>
                </form>
            </div>
        </header>

        <!-- ===== STATS ===== -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ verta()->format('%d') }}</div>
                <div class="stat-label">{{ verta()->format('%B %Y') }}</div>
                <span class="stat-change up">خوش آمدید</span>
            </div>

            <div class="stat-card">
                <div class="stat-number">{{ auth()->user()->name }}</div>
                <div class="stat-label">نام کاربری</div>
            </div>

            <div class="stat-card">
                <div class="stat-number">{{ auth()->user()->email }}</div>
                <div class="stat-label">ایمیل</div>
            </div>

            <div class="stat-card">
                <div class="stat-number">{{ auth()->user()->phone }}</div>
                <div class="stat-label">شماره موبایل</div>
            </div>
        </div>

        <!-- ===== SECTION BUTTONS (بر اساس دسترسی) ===== -->
        <div class="section-title">بخش‌های قابل دسترس</div>
        <div class="section-grid">
            
            @if(auth()->user()->hasModuleAccess('cars'))
                <a href="{{ route('cars.index') }}" class="btn-section cars">
                    ماشین‌ها
                </a>
            @endif

            @if(auth()->user()->hasModuleAccess('houses'))
                <a href="{{ route('houses.index') }}" class="btn-section houses">
                    منازل
                </a>
            @endif

            @if(auth()->user()->hasModuleAccess('persons'))
                <a href="{{ route('persons.index') }}" class="btn-section persons">
                    اشخاص
                </a>
            @endif

            @if(auth()->user()->hasModuleAccess('organizations'))
                <a href="{{ route('organizations.index') }}" class="btn-section organizations">
                    ارگان‌ها
                </a>
            @endif

            @if(auth()->user()->hasModuleAccess('expenses'))
                <a href="{{ route('expenses.index') }}" class="btn-section expenses">
                    هزینه‌ها
                </a>
            @endif

            <a href="{{ route('profile.edit') }}" class="btn-section profile">
                ویرایش پروفایل
            </a>

        </div>

        <!-- ===== اگر دسترسی به هیچ بخشی ندارد ===== -->
        @php
            $hasAccess = auth()->user()->hasModuleAccess('cars') || 
                         auth()->user()->hasModuleAccess('houses') || 
                         auth()->user()->hasModuleAccess('persons') || 
                         auth()->user()->hasModuleAccess('organizations') || 
                         auth()->user()->hasModuleAccess('expenses');
        @endphp

        @if(!$hasAccess)
            <div class="access-denied">
                <p>شما به هیچ بخشی دسترسی ندارید</p>
                <p style="font-size:14px; color:#9ca3af;">برای دریافت دسترسی با مدیر سیستم تماس بگیرید</p>
            </div>
        @endif

        <!-- ===== WELCOME CARD ===== -->
        <div class="table-wrapper" style="margin-top:30px;">
            <div class="table-header">
                <h3>به پنل حسابداری خوش آمدید</h3>
                <div class="count">{{ verta()->format('%A %d %B %Y') }}</div>
            </div>
            <div style="padding: 20px; text-align: center; color: #6b7280; font-size: 16px; line-height: 2;">
                <p style="font-size: 20px; color: #1a1a2e; font-weight: 600;">
                    {{ auth()->user()->name }} عزیز، خوش آمدید
                </p>
                <p>شما به عنوان حسابدار در سیستم ثبت نام کرده‌اید.</p>
                <p style="font-size: 14px; color: #9ca3af;">
                    تاریخ امروز: {{ verta()->format('%A %d %B %Y') }}
                </p>
            </div>
        </div>

    </div>

</body>
</html>