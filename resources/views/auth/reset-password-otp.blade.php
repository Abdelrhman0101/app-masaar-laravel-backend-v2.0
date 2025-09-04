<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعادة تعيين كلمة المرور - معسار</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
        }
        .btn-outline-secondary {
            border-radius: 25px;
            padding: 12px 30px;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .otp-input {
            width: 60px;
            height: 60px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 0 5px;
        }
        .step {
            display: none;
        }
        .step.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-body p-5">
                        <!-- Step 1: Request OTP -->
                        <div id="step1" class="step active">
                            <div class="text-center mb-4">
                                <i class="fas fa-key fa-3x text-primary mb-3"></i>
                                <h3 class="card-title">إعادة تعيين كلمة المرور</h3>
                                <p class="text-muted">أدخل بريدك الإلكتروني لإرسال رمز التأكيد</p>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    @foreach ($errors->all() as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('otp.send-password-reset') }}">
                                @csrf
                                <div class="mb-4">
                                    <label for="email" class="form-label">البريد الإلكتروني</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="{{ old('email') }}" required>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i>إرسال رمز التأكيد
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Step 2: Verify OTP -->
                        <div id="step2" class="step {{ session('otp_sent') ? 'active' : '' }}">
                            <div class="text-center mb-4">
                                <i class="fas fa-shield-check fa-3x text-primary mb-3"></i>
                                <h3 class="card-title">تأكيد الرمز</h3>
                                <p class="text-muted">تم إرسال رمز التأكيد إلى بريدك الإلكتروني</p>
                            </div>

                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('otp.verify-password-reset') }}">
                                @csrf
                                <input type="hidden" name="email" value="{{ session('reset_email') ?? old('email') }}">
                                
                                <div class="mb-4">
                                    <label class="form-label">أدخل رمز التأكيد المكون من 6 أرقام</label>
                                    <div class="d-flex justify-content-center">
                                        <input type="text" class="form-control otp-input" name="otp[]" maxlength="1" required>
                                        <input type="text" class="form-control otp-input" name="otp[]" maxlength="1" required>
                                        <input type="text" class="form-control otp-input" name="otp[]" maxlength="1" required>
                                        <input type="text" class="form-control otp-input" name="otp[]" maxlength="1" required>
                                        <input type="text" class="form-control otp-input" name="otp[]" maxlength="1" required>
                                        <input type="text" class="form-control otp-input" name="otp[]" maxlength="1" required>
                                    </div>
                                    <input type="hidden" name="verification_code" id="fullOtp">
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-check me-2"></i>تأكيد الرمز
                                    </button>
                                </div>
                            </form>

                            <div class="text-center mt-4">
                                <p class="text-muted mb-2">لم تستلم الرمز؟</p>
                                <form method="POST" action="{{ route('otp.send-password-reset') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="email" value="{{ session('reset_email') ?? old('email') }}">
                                    <button type="submit" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-redo me-2"></i>إعادة إرسال الرمز
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Step 3: Reset Password -->
                        <div id="step3" class="step {{ session('otp_verified') ? 'active' : '' }}">
                            <div class="text-center mb-4">
                                <i class="fas fa-lock fa-3x text-primary mb-3"></i>
                                <h3 class="card-title">كلمة مرور جديدة</h3>
                                <p class="text-muted">أدخل كلمة المرور الجديدة</p>
                            </div>

                            <form method="POST" action="{{ route('otp.reset-password') }}">
                                @csrf
                                <input type="hidden" name="email" value="{{ session('reset_email') ?? old('email') }}">
                                <input type="hidden" name="token" value="{{ session('reset_token') }}">
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">كلمة المرور الجديدة</label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           minlength="8" required>
                                    <div class="form-text">يجب أن تكون كلمة المرور 8 أحرف على الأقل</div>
                                </div>

                                <div class="mb-4">
                                    <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                                    <input type="password" class="form-control" id="password_confirmation" 
                                           name="password_confirmation" required>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>حفظ كلمة المرور الجديدة
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="text-center mt-3">
                            <a href="{{ route('login') }}" class="text-decoration-none">
                                <i class="fas fa-arrow-right me-2"></i>العودة لتسجيل الدخول
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // OTP Input Handler
        document.addEventListener('DOMContentLoaded', function() {
            const otpInputs = document.querySelectorAll('.otp-input');
            const fullOtpInput = document.getElementById('fullOtp');

            if (otpInputs.length > 0) {
                otpInputs.forEach((input, index) => {
                    input.addEventListener('input', function(e) {
                        const value = e.target.value;
                        
                        // Only allow numbers
                        if (!/^[0-9]$/.test(value)) {
                            e.target.value = '';
                            return;
                        }

                        // Move to next input
                        if (value && index < otpInputs.length - 1) {
                            otpInputs[index + 1].focus();
                        }

                        // Update hidden input with full OTP
                        updateFullOtp();
                    });

                    input.addEventListener('keydown', function(e) {
                        // Move to previous input on backspace
                        if (e.key === 'Backspace' && !e.target.value && index > 0) {
                            otpInputs[index - 1].focus();
                        }
                    });

                    input.addEventListener('paste', function(e) {
                        e.preventDefault();
                        const pastedData = e.clipboardData.getData('text');
                        const digits = pastedData.replace(/\D/g, '').slice(0, 6);
                        
                        digits.split('').forEach((digit, i) => {
                            if (otpInputs[i]) {
                                otpInputs[i].value = digit;
                            }
                        });
                        
                        updateFullOtp();
                    });
                });

                function updateFullOtp() {
                    const otp = Array.from(otpInputs).map(input => input.value).join('');
                    if (fullOtpInput) {
                        fullOtpInput.value = otp;
                    }
                }
            }

            // Show appropriate step based on session data
            @if(session('otp_sent'))
                document.getElementById('step1').classList.remove('active');
                document.getElementById('step2').classList.add('active');
            @endif

            @if(session('otp_verified'))
                document.getElementById('step1').classList.remove('active');
                document.getElementById('step2').classList.remove('active');
                document.getElementById('step3').classList.add('active');
            @endif
        });
    </script>
</body>
</html>