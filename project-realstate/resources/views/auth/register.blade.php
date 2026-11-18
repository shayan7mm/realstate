<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ثبت‌نام در سیستم حسابداری صبا</title>
    <link rel="stylesheet" href="{{ asset('css/Style.css') }}">
    <style>
        /* ===== REGISTER PAGE STYLES ===== */
        .register-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-card {
            width: 100%;
            max-width: 480px;
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.06);
            padding: 40px 36px;
        }

        .register-card .logo-area {
            text-align: center;
            margin-bottom: 28px;
        }

        .register-card .logo-area .logo-box {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            box-shadow: 0 8px 30px rgba(79, 70, 229, 0.25);
            margin: 0 auto 14px;
        }

        .register-card .logo-area h2 {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 4px;
        }

        .register-card .logo-area p {
            font-size: 14px;
            color: #6b7280;
        }

        .register-card .form-group {
            margin-bottom: 18px;
        }

        .register-card .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #1a1a2e;
            margin-bottom: 6px;
        }

        .register-card .form-group input {
            width: 100%;
            padding: 12px 16px;
            background: #f8fafc;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            color: #1a1a2e;
            font-size: 15px;
            transition: all 0.3s;
            outline: none;
            font-family: inherit;
        }

        .register-card .form-group input:focus {
            border-color: #4f46e5;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.06);
        }

        .register-card .form-group input::placeholder {
            color: #9ca3af;
        }

        .register-card .form-group .error-text {
            color: #dc2626;
            font-size: 13px;
            margin-top: 4px;
            display: block;
        }

        .register-card .form-group .help-text {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 4px;
        }

        .register-card .btn-register {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border: none;
            border-radius: 12px;
            color: #ffffff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(79, 70, 229, 0.20);
            font-family: inherit;
            margin-top: 8px;
        }

        .register-card .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(79, 70, 229, 0.30);
        }

        .register-card .login-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #f1f3f5;
            font-size: 14px;
            color: #6b7280;
        }

        .register-card .login-link a {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 500;
        }

        .register-card .login-link a:hover {
            text-decoration: underline;
        }

        .register-card .session-status {
            background: rgba(239, 68, 68, 0.06);
            border: 1px solid rgba(239, 68, 68, 0.12);
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            color: #dc2626;
            font-size: 14px;
            text-align: center;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 480px) {
            .register-card {
                padding: 28px 20px;
                border-radius: 18px;
            }

            .register-card .logo-area .logo-box {
                width: 52px;
                height: 52px;
                font-size: 20px;
            }

            .register-card .logo-area h2 {
                font-size: 20px;
            }

            .register-card .form-group input {
                padding: 10px 14px;
                font-size: 14px;
            }

            .register-card .btn-register {
                padding: 12px;
                font-size: 15px;
            }
        }

        @media (max-width: 380px) {
            .register-card {
                padding: 20px 16px;
            }

            .register-card .logo-area .logo-box {
                width: 44px;
                height: 44px;
                font-size: 17px;
            }

            .register-card .logo-area h2 {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>

    <div class="bg-wrapper"></div>

    <div class="register-wrapper">

        <div class="register-card">

            <!-- ===== LOGO ===== -->
            <div class="logo-area">
                <div class="logo-box">صبا</div>
                <h2>ثبت‌نام در سیستم</h2>
                <p>سیستم حسابداری صبا</p>
            </div>

            <!-- ===== SESSION STATUS ===== -->
            @if(session('error'))
                <div class="session-status">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="session-status">
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <!-- ===== FORM ===== -->
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="form-group">
                    <label for="name">نام کامل</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}" 
                        placeholder="نام و نام خانوادگی"
                        required 
                        autofocus
                    >
                    @error('name')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">ایمیل</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        placeholder="example@email.com"
                        required
                    >
                    @error('email')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Phone -->
                <div class="form-group">
                    <label for="phone">شماره موبایل</label>
                    <input 
                        type="text" 
                        id="phone" 
                        name="phone" 
                        value="{{ old('phone') }}" 
                        placeholder="09123456789"
                        dir="ltr"
                        required
                    >
                    <div class="help-text">مثال: 09123456789</div>
                    @error('phone')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">رمز عبور</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="حداقل ۸ کاراکتر"
                        required
                    >
                    <div class="help-text">حداقل ۸ کاراکتر</div>
                    @error('password')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="password_confirmation">تکرار رمز عبور</label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        placeholder="تکرار رمز عبور"
                        required
                    >
                    @error('password_confirmation')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-register">
                    ثبت‌نام در سیستم
                </button>

            </form>

            <!-- ===== LOGIN LINK ===== -->
            <div class="login-link">
                قبلاً ثبت‌نام کرده‌اید؟
                <a href="{{ route('login') }}">ورود به سیستم</a>
            </div>

        </div>

    </div>

</body>
</html>