<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سیستم حسابداری صبا</title>
    <link rel="stylesheet" href="{{ asset('css/Style.css') }}">
    <style>
        /* ===== LANDING PAGE STYLES ===== */
        .hero-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 80vh;
            text-align: center;
            padding: 40px 20px;
        }

        .hero-section .logo-big {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            font-weight: 800;
            color: #ffffff;
            box-shadow: 0 12px 40px rgba(79, 70, 229, 0.3);
            margin-bottom: 30px;
        }

        .hero-section h1 {
            font-size: 48px;
            font-weight: 800;
            color: #1a1a2e;
            margin-bottom: 12px;
            letter-spacing: -1px;
        }

        .hero-section h1 span {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-section .subtitle {
            font-size: 20px;
            color: #6b7280;
            margin-bottom: 8px;
            font-weight: 400;
        }

        .hero-section .description {
            max-width: 600px;
            font-size: 16px;
            color: #6b7280;
            line-height: 1.8;
            margin: 16px auto 32px;
        }

        .hero-buttons {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .hero-buttons .btn-hero {
            padding: 14px 40px;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .hero-buttons .btn-hero.primary {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #ffffff;
            box-shadow: 0 4px 20px rgba(79, 70, 229, 0.25);
        }

        .hero-buttons .btn-hero.primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 32px rgba(79, 70, 229, 0.35);
        }

        .hero-buttons .btn-hero.secondary {
            background: #ffffff;
            color: #1a1a2e;
            border: 2px solid #e5e7eb;
        }

        .hero-buttons .btn-hero.secondary:hover {
            border-color: #4f46e5;
            color: #4f46e5;
            transform: translateY(-3px);
        }

        /* ===== FEATURES ===== */
        .features-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            padding: 40px 0 60px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 28px 24px;
            border: 1px solid rgba(0, 0, 0, 0.04);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.03);
            text-align: center;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.06);
            border-color: rgba(0, 0, 0, 0.06);
        }

        .feature-card .feature-icon {
            font-size: 36px;
            display: block;
            margin-bottom: 12px;
        }

        .feature-card h3 {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 8px;
        }

        .feature-card p {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.6;
        }

        /* ===== FOOTER ===== */
        .footer {
            text-align: center;
            padding: 30px 20px;
            border-top: 1px solid rgba(0, 0, 0, 0.04);
            color: #9ca3af;
            font-size: 14px;
        }

        .footer a {
            color: #4f46e5;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 32px;
            }

            .hero-section .subtitle {
                font-size: 17px;
            }

            .hero-section .description {
                font-size: 15px;
                padding: 0 16px;
            }

            .hero-buttons .btn-hero {
                padding: 12px 28px;
                font-size: 15px;
                width: 100%;
                text-align: center;
            }

            .hero-buttons {
                flex-direction: column;
                width: 100%;
                max-width: 320px;
            }

            .features-section {
                grid-template-columns: 1fr;
                padding: 20px 0 40px;
            }

            .logo-box {
                width: 42px;
                height: 42px;
                font-size: 16px;
            }

            .header-left h1 {
                font-size: 18px;
            }

            .header-right {
                gap: 10px;
            }

            .header-right .btn-hero.small {
                padding: 8px 16px;
                font-size: 13px;
            }
        }

        @media (max-width: 480px) {
            .hero-section {
                min-height: 70vh;
                padding: 20px 12px;
            }

            .hero-section h1 {
                font-size: 26px;
            }

            .hero-section .logo-big {
                width: 72px;
                height: 72px;
                font-size: 28px;
            }

            .hero-section .description {
                font-size: 14px;
            }

            .feature-card {
                padding: 20px 16px;
            }

            .feature-card .feature-icon {
                font-size: 28px;
            }

            .header {
                padding: 12px 16px;
                flex-direction: column;
                gap: 10px;
            }

            .header-right {
                flex-wrap: wrap;
                justify-content: center;
            }
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
                    <h1>سیستم حسابداری صبا</h1>
                    <div class="subtitle">مدیریت هوشمند مالی</div>
                </div>
            </div>
            <div class="header-right">
                @if(Auth::check())
                    <a href="{{ route('dashboard') }}" class="btn-hero small" style="padding:10px 24px; border-radius:12px; font-size:14px; font-weight:600; text-decoration:none; background:linear-gradient(135deg, #4f46e5, #7c3aed); color:#ffffff; box-shadow:0 4px 16px rgba(79,70,229,0.2);">
                        ورود به داشبورد
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-hero small" style="padding:10px 24px; border-radius:12px; font-size:14px; font-weight:600; text-decoration:none; background:linear-gradient(135deg, #4f46e5, #7c3aed); color:#ffffff; box-shadow:0 4px 16px rgba(79,70,229,0.2);">
                        ورود به سیستم
                    </a>
                @endif
            </div>
        </header>

        <!-- ===== HERO SECTION ===== -->
        <section class="hero-section">
            <div class="logo-big">صبا</div>

            <h1>به <span>سیستم حسابداری صبا</span> خوش آمدید</h1>

            <p class="subtitle">مدیریت جامع مالی، ساده و هوشمند</p>

            <p class="description">
                سیستم حسابداری صبا ابزاری کامل برای مدیریت امور مالی شماست.
                با این سیستم می‌توانید به راحتی ماشین‌ها، منازل، اشخاص، ارگان‌ها و هزینه‌های جانبی را مدیریت کنید.
                تمام تراکنش‌های مالی خود را ثبت، پیگیری و گزارش بگیرید.
            </p>

            <div class="hero-buttons">
                @if(Auth::check())
                    <a href="{{ route('dashboard') }}" class="btn-hero primary">ورود به داشبورد</a>
                @else
                    <a href="{{ route('login') }}" class="btn-hero primary">ورود به سیستم</a>
                    
                @endif
            </div>
        </section>

        <!-- ===== FEATURES ===== -->
        <section class="features-section">
            <div class="feature-card">
                <span class="feature-icon">مدیریت</span>
                <h3>مدیریت جامع</h3>
                <p>مدیریت ماشین‌ها، منازل، اشخاص، ارگان‌ها و هزینه‌های جانبی در یک پلتفرم یکپارچه</p>
            </div>

            <div class="feature-card">
                <span class="feature-icon">تراکنش</span>
                <h3>ثبت تراکنش‌ها</h3>
                <p>ثبت ورودی و خروجی با جزئیات کامل شامل مبلغ، تاریخ، شماره کارت مبدأ و مقصد و تصویر فیش</p>
            </div>

            <div class="feature-card">
                <span class="feature-icon">کارت مالی</span>
                <h3>کارت مالی هوشمند</h3>
                <p>مشاهده جمع ورودی‌ها، خروجی‌ها و سود خالص هر بخش به صورت جداگانه</p>
            </div>

            <div class="feature-card">
                <span class="feature-icon">گزارش</span>
                <h3>گزارشات دقیق</h3>
                <p>دریافت گزارشات مالی جامع و مشاهده وضعیت مالی کلی سیستم</p>
            </div>
        </section>

        <!-- ===== FOOTER ===== -->
        <div class="footer">
            <p>
                سیستم حسابداری صبا &copy; ۱۴۰۴
                <span style="color:#d1d5db; margin:0 8px;">|</span>
                طراحی و توسعه توسط تیم <a href="https://shayanwebmaster.ir">شایان وب مستر</a>
            </p>
        </div>

    </div>

</body>
</html>