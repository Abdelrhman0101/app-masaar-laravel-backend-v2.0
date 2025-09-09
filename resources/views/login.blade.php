<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #6c757d 0%, #FC8700 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 40px;
            position: relative;
            overflow: hidden;
            animation: slideUp 0.8s ease-out;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #FC8700, #6c757d, #FC8700, #495057);
            background-size: 300% 100%;
            animation: gradientShift 3s ease infinite;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 15px 40px rgba(252, 135, 0, 0.4), 0 5px 15px rgba(0, 0, 0, 0.3);
            margin-bottom: 15px;
          padding:15px;
            transition: transform 0.3s ease;
            border: 3px solid #FC8700;
        }

        .logo-image:hover {
            transform: scale(1.1) rotate(5deg);
        }

        .app-name {
            font-size: 32px;
            font-weight: 700;
            background: linear-gradient(135deg, #FC8700, #6c757d);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 5px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .app-subtitle {
            color: #666;
            font-size: 14px;
            font-weight: 500;
        }

        .login-title {
            text-align: center;
            color: #333;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 30px;
            position: relative;
        }

        .login-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, #FC8700, #6c757d);
            border-radius: 2px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label {
            color: #555;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control {
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            padding: 15px 20px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
            position: relative;
        }

        .form-control:focus {
            border-color: #FC8700;
            box-shadow: 0 0 0 0.2rem rgba(252, 135, 0, 0.25);
            background: rgba(255, 255, 255, 1);
            transform: translateY(-2px);
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 18px;
            transition: color 0.3s ease;
        }

        .form-control:focus + .input-icon {
            color: #FC8700;
        }

        .login-btn {
            background: linear-gradient(135deg, #FC8700 0%, #6c757d 100%);
            border: none;
            border-radius: 12px;
            padding: 15px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(252, 135, 0, 0.4);
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .error-message {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
            font-weight: 500;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: floatShapes 15s infinite linear;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 5s;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 10s;
        }

        @keyframes floatShapes {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 0.7;
            }
            50% {
                transform: translateY(-20px) rotate(180deg);
                opacity: 0.3;
            }
            100% {
                transform: translateY(0) rotate(360deg);
                opacity: 0.7;
            }
        }

        /* Social Login Styles */
        .social-login-section {
            margin-top: 25px;
        }
        
        .divider {
            position: relative;
            text-align: center;
            margin: 20px 0;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: rgba(255, 255, 255, 0.2);
        }
        
        .divider span {
            background: rgba(255, 255, 255, 0.1);
            padding: 0 15px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }
        
        .google-login-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px 20px;
            background: #fff;
            color: #333;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 500;
            font-size: 16px;
            transition: all 0.3s ease;
            border: 1px solid #dadce0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .google-login-btn:hover {
            background: #f8f9fa;
            color: #333;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        @media (max-width: 768px) {
            .login-card {
                margin: 20px;
                padding: 30px 25px;
              
            }
            
            .app-name {
                font-size: 24px;
            }
            
            .login-title {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="login-container">
        <div class="login-card">
            <div class="logo-section">
                <img src="https://msar.app/storage/uploads/images/masar.png" alt="Masar Logo" class="logo-image">
                <div class="app-name">مسار - Masar</div>
                <div class="app-subtitle">Admin Dashboard</div>
            </div>
            
            <h3 class="login-title">تسجيل الدخول</h3>
            
            <form id="loginForm">
                <div class="form-group">
                    <label for="email" class="form-label">البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" class="form-control" required autocomplete="email" placeholder="أدخل البريد الإلكتروني">
                    <i class="fas fa-envelope input-icon"></i>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">كلمة المرور</label>
                    <input type="password" id="password" name="password" class="form-control" required autocomplete="current-password" placeholder="أدخل كلمة المرور">
                    <i class="fas fa-lock input-icon"></i>
                </div>
                
                <div id="errorMsg" class="error-message d-none"></div>
                
                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    دخول
                </button>
            </form>
            
            
        </div>
    </div>

<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    document.getElementById('errorMsg').classList.add('d-none');

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    console.log("Submitting login form with:", { email, password });

    try {
        const response = await fetch('/api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });

        console.log("Raw response:", response);

        // Check HTTP status
        console.log("Response status:", response.status);

        const data = await response.json();

        // Log the entire response data
        console.log("Parsed response JSON:", data);

        if (data.status && data.user && data.user.user_type === 'admin') {
            console.log("Login successful! Storing token and user data in localStorage...");
            localStorage.setItem('token', data.token);
            localStorage.setItem('user', JSON.stringify(data.user));
            console.log("Token:", data.token);
            console.log("User:", data.user);
            console.log("Redirecting to /dashboard...");
            window.location.href = '/dashboard';
        } else {
            console.log("Login failed! Message:", data.message);
            document.getElementById('errorMsg').innerText = data.message || 'Login failed';
            document.getElementById('errorMsg').classList.remove('d-none');
        }
    } catch (error) {
        console.error("Error during login request:", error);
        document.getElementById('errorMsg').innerText = 'An error occurred. Please try again.';
        document.getElementById('errorMsg').classList.remove('d-none');
    }
});
</script>
</body>
</html>
