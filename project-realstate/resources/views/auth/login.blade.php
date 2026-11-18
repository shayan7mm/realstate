<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود به سیستم حسابداری صبا</title>
    <link rel="stylesheet" href="{{ asset('css/Style.css') }}">
    <style>
        /* ===== LOGIN PAGE STYLES ===== */
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.06);
            padding: 40px 36px;
        }

        .login-card .logo-area {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-card .logo-area .logo-box {
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

        .login-card .logo-area h2 {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 4px;
        }

        .login-card .logo-area p {
            font-size: 14px;
            color: #6b7280;
        }

        .login-card .logo-area p a {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 500;
        }

        .login-card .logo-area p a:hover {
            text-decoration: underline;
        }

        .login-card .form-group {
            margin-bottom: 20px;
        }

        .login-card .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #1a1a2e;
            margin-bottom: 6px;
        }

        .login-card .form-group input {
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

        .login-card .form-group input:focus {
            border-color: #4f46e5;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.06);
        }

        .login-card .form-group input::placeholder {
            color: #9ca3af;
        }

        .login-card .form-group .error-text {
            color: #dc2626;
            font-size: 13px;
            margin-top: 4px;
            display: block;
        }

        .login-card .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .login-card .form-options label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #6b7280;
            cursor: pointer;
        }

        .login-card .form-options label input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #4f46e5;
            cursor: pointer;
        }

        .login-card .form-options a {
            font-size: 14px;
            color: #4f46e5;
            text-decoration: none;
        }

        .login-card .form-options a:hover {
            text-decoration: underline;
        }

        .login-card .btn-login {
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
        }

        .login-card .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(79, 70, 229, 0.30);
        }

        .login-card .register-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #f1f3f5;
            font-size: 14px;
            color: #6b7280;
        }

        .login-card .register-link a {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 500;
        }

        .login-card .register-link a:hover {
            text-decoration: underline;
        }

        .login-card .session-status {
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.15);
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            color: #059669;
            font-size: 14px;
            text-align: center;
        }

        .login-card .session-status.error {
            background: rgba(239, 68, 68, 0.06);
            border-color: rgba(239, 68, 68, 0.12);
            color: #dc2626;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 480px) {
            .login-card {
                padding: 28px 20px;
                border-radius: 18px;
            }

            .login-card .logo-area .logo-box {
                width: 52px;
                height: 52px;
                font-size: 20px;
            }

            .login-card .logo-area h2 {
                font-size: 20px;
            }

            .login-card .form-group input {
                padding: 10px 14px;
                font-size: 14px;
            }

            .login-card .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .login-card .btn-login {
                padding: 12px;
                font-size: 15px;
            }
        }

        @media (max-width: 380px) {
            .login-card {
                padding: 20px 16px;
            }

            .login-card .logo-area .logo-box {
                width: 44px;
                height: 44px;
                font-size: 17px;
            }

            .login-card .logo-area h2 {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>

    <div class="bg-wrapper"></div>

    <div class="login-wrapper">

        <div class="login-card">

            <!-- ===== LOGO ===== -->
            <div class="logo-area">
                <div class="logo-box">صبا</div>
                <h2>ورود به حساب</h2>
                <p>سیستم حسابداری صبا</p>
            </div>

            <!-- ===== SESSION STATUS ===== -->
            @if(session('status'))
                <div class="session-status">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->has('login'))
                <div class="session-status error">
                    {{ $errors->first('login') }}
                </div>
            @endif

            <!-- ===== FORM ===== -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email or Phone -->
                <div class="form-group">
                    <label for="login">ایمیل یا شماره موبایل</label>
                    <input 
                        type="text" 
                        id="login" 
                        name="login" 
                        value="{{ old('login') }}" 
                        placeholder="ایمیل یا شماره موبایل خود را وارد کنید"
                        required 
                        autofocus
                    >
                    @error('login')
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
                        placeholder="رمز عبور خود را وارد کنید"
                        required 
                        autocomplete="current-password"
                    >
                    @error('password')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Options -->
               

                <!-- Submit -->
                <button type="submit" class="btn-login">
                    ورود به سیستم
                </button>

            </form>

         

        </div>

    </div>

</body>
</html>