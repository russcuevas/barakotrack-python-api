<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | UB Barako Track Lost & Found</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #752738;
            --secondary-color: #fec452;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e1e2d 0%, #3a151f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1.5rem;
        }
        .login-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            overflow: hidden;
            width: 100%;
            max-width: 440px;
        }
        .login-header {
            background-color: var(--primary-color);
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
        }
        .login-header h3 {
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .login-header h3 span {
            color: var(--secondary-color);
        }
        .btn-primary-ub {
            background-color: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
            padding: 0.75rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .btn-primary-ub:hover {
            background-color: #5a1e2c;
            color: white;
        }
        .quick-demo-box {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            padding: 1rem;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <i class="bi bi-shield-check display-4 text-warning mb-2 d-inline-block"></i>
        <h3>BARAKO <span>TRACK</span></h3>
        <p class="m-0 text-white-50 fs-7">University of Batangas Campus Lost & Found</p>
    </div>

    <div class="p-4 p-md-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show fs-7 py-2" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show fs-7 py-2" role="alert">
                {{ $errors->first() }}
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" id="loginForm">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold fs-7">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" id="emailInput" class="form-control" placeholder="user@ub.edu.ph" required value="{{ old('email') }}">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold fs-7">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="passwordInput" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 fs-7">
                <div class="form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label class="form-check-label text-muted" for="remember">Remember me</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary-ub w-100 mb-4">
                Sign In to Portal <i class="bi bi-arrow-right-short fs-5"></i>
            </button>
        </form>

        <!-- Quick Demo Login Helper Buttons -->
        <div class="quick-demo-box text-center">
            <small class="fw-bold text-uppercase text-muted d-block mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">⚡ One-Click Demo Logins</small>
            <div class="d-grid gap-2">
                <button type="button" class="btn btn-sm btn-outline-dark fw-semibold" onclick="quickLogin('student@ub.edu.ph', 'password123')">
                    🎓 Login as Student (Decsten Matibag)
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger fw-semibold" onclick="quickLogin('admin@ub.edu.ph', 'password123')">
                    🛡️ Login as SAO Administrator
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function quickLogin(email, password) {
        document.getElementById('emailInput').value = email;
        document.getElementById('passwordInput').value = password;
        document.getElementById('loginForm').submit();
    }
</script>
</body>
</html>
