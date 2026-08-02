<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log into BarakoTrack | UB Campus Lost & Found</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('logo/favicon.png') }}">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #752738;
            --primary-hover: #5a1e2c;
            --secondary-color: #fec452;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
            color: #1e293b;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
        }

        .login-split-container {
            min-height: 100vh;
        }

        .left-panel {
            background-color: #ffffff;
            padding: 2.5rem 3.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid #f1f5f9;
        }

        .left-visual-img {
            max-width: 440px;
            width: 100%;
            border-radius: 32px !important;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
            object-fit: cover;
        }

        .hero-headline {
            font-size: 3.2rem;
            font-weight: 800;
            line-height: 1.08;
            letter-spacing: -1.2px;
            color: #1e293b;
        }

        .hero-headline span {
            color: var(--primary-color);
        }

        .right-panel {
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 2rem;
        }

        .login-form-box {
            max-width: 380px;
            width: 100%;
        }

        .form-control-custom {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 0.65rem 1rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control-custom:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(117, 39, 56, 0.12);
        }

        .btn-login-maroon {
            background-color: var(--primary-color);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 0.7rem;
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .btn-login-maroon:hover {
            background-color: var(--primary-hover);
            color: #ffffff;
            transform: translateY(-1px);
        }

        .btn-outline-create {
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            border-radius: 8px;
            padding: 0.5rem 1.25rem;
            font-weight: 600;
            font-size: 0.85rem;
            background: transparent;
            transition: all 0.2s ease;
        }

        .btn-outline-create:hover {
            background-color: rgba(117, 39, 56, 0.05);
            color: var(--primary-hover);
        }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0 login-split-container">
        <!-- Left Panel: Graphic & Headline -->
        <div class="col-lg-6 left-panel d-none d-lg-flex">
            <div>
                <img src="{{ asset('logo/favicon.png') }}" width="42" height="42" alt="UB Seal">
            </div>

            <div class="text-center my-auto py-4">
                <img src="{{ asset('logo/login-visual.png') }}" class="left-visual-img" alt="Empowering Brahman Students">
            </div>

            <div>
                <h1 class="hero-headline m-0">
                    Empowering<br>
                    Brahman<br>
                    <span>Excellence.</span>
                </h1>
            </div>
        </div>

        <!-- Right Panel: Login Form -->
        <div class="col-lg-6 right-panel">
            <div class="login-form-box text-center">
                <!-- Top Seal & University Name -->
                <div class="mb-4">
                    <img src="{{ asset('logo/favicon.png') }}" width="44" height="44" class="mb-2" alt="UB Seal">
                    <div class="fw-bold uppercase" style="font-size: 0.72rem; letter-spacing: 1.5px; color: #475569;">
                        UNIVERSITY OF BATANGAS LIPA CAMPUS
                    </div>
                </div>

                <h5 class="fw-bold text-dark mb-4 text-start">Log into BarakoTrack</h5>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show fs-7 py-2 text-start" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show fs-7 py-2 text-start" role="alert">
                        {{ $errors->first() }}
                        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" id="loginForm">
                    @csrf
                    <div class="mb-3 text-start">
                        <input type="email" name="email" id="emailInput" class="form-control form-control-custom" placeholder="Email" required value="{{ old('email') }}">
                    </div>

                    <div class="mb-3 text-start">
                        <input type="password" name="password" id="passwordInput" class="form-control form-control-custom" placeholder="Password" required>
                    </div>

                    <button type="submit" class="btn btn-login-maroon w-100 mb-3">
                        Log in
                    </button>

                    <div class="mb-4">
                        <a href="#" class="text-muted text-decoration-none fs-7" style="color: #64748b;">Forgot password?</a>
                    </div>
                </form>

                <hr class="my-4" style="opacity: 0.12;">

                <div class="mb-4">
                    <a href="#" class="btn btn-outline-create text-decoration-none">
                        Create new account
                    </a>
                </div>

                <!-- Quick One-Click Demo Login Box -->
                <div class="p-3 bg-light rounded-3 border border-dashed text-center mt-3">
                    <small class="fw-bold text-uppercase text-muted d-block mb-2" style="font-size: 0.65rem; letter-spacing: 0.5px;">⚡ One-Click Demo Logins</small>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-dark w-100 fw-semibold fs-7" onclick="quickLogin('student@ub.edu.ph', 'password123')">
                            🎓 Student
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger w-100 fw-semibold fs-7" onclick="quickLogin('admin@ub.edu.ph', 'password123')">
                            🛡️ SAO Admin
                        </button>
                    </div>
                </div>
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
