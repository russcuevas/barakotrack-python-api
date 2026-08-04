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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
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
                    <img src="{{ asset('logo/login-visual.png') }}" class="left-visual-img"
                        alt="Empowering Brahman Students">
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
                        <img src="{{ asset('logo/favicon.png') }}" width="44" height="44" class="mb-2"
                            alt="UB Seal">
                        <div class="fw-bold uppercase"
                            style="font-size: 0.72rem; letter-spacing: 1.5px; color: #475569;">
                            UNIVERSITY OF BATANGAS LIPA CAMPUS
                        </div>
                    </div>

                    <h5 class="fw-bold text-dark mb-4 text-start">Log into BarakoTrack</h5>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show fs-7 py-2 text-start"
                            role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any() && !$errors->hasBag('registration') && !($errors->has('name') || $errors->has('student_id_number') || $errors->has('password_confirmation')))
                        <div class="alert alert-danger alert-dismissible fade show fs-7 py-2 text-start" role="alert">
                            {{ $errors->first() }}
                            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST" id="loginForm">
                        @csrf
                        <div class="mb-3 text-start">
                            <input type="email" name="email" id="emailInput"
                                class="form-control form-control-custom" placeholder="Email" required
                                value="{{ old('email') }}">
                        </div>

                        <div class="mb-3 text-start">
                            <input type="password" name="password" id="passwordInput"
                                class="form-control form-control-custom" placeholder="Password" required>
                        </div>

                        <button type="submit" class="btn btn-login-maroon w-100 mb-3">
                            Log in
                        </button>

                        <div class="mb-4">
                            <a href="#" class="text-muted text-decoration-none fs-7"
                                style="color: #64748b;">Forgot password?</a>
                        </div>
                    </form>

                    <hr class="my-4" style="opacity: 0.12;">

                    <div class="mb-4">
                        <button type="button" class="btn btn-outline-create w-100" data-bs-toggle="modal"
                            data-bs-target="#registerModal">
                            Create new account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Student Account Registration -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-lg border-0" style="border-radius: 16px;">
                <div class="modal-header text-white"
                    style="background-color: var(--primary-color); border-top-left-radius: 16px; border-top-right-radius: 16px;">
                    <h5 class="modal-header-title fw-bold m-0 fs-6">
                        <i class="bi bi-person-plus-fill me-2 text-warning"></i> Student Account Registration
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-start">
                    <div class="alert alert-warning py-2 fs-7 mb-3 d-flex align-items-center gap-2 border-warning">
                        <i class="bi bi-shield-lock-fill text-warning fs-5"></i>
                        <div>
                            <strong>Student Registration Only</strong> <br> You must use your official University of
                            Batangas <code>@ub.edu.ph</code> email address.
                        </div>
                    </div>

                    @if ($errors->hasBag('registration') || ($errors->any() && (old('student_id_number') || old('name') || $errors->has('name') || $errors->has('student_id_number') || $errors->has('password_confirmation'))))
                        @php
                            $regErrors = $errors->hasBag('registration') ? $errors->registration->all() : $errors->all();
                        @endphp
                        <div class="alert alert-danger alert-dismissible fade show fs-7 py-2 mb-3 text-start border-danger" role="alert">
                            <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Registration Error:</div>
                            <ul class="mb-0 ps-3">
                                @foreach ($regErrors as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('register') }}" method="POST" id="registrationForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold fs-7 mb-1 text-dark">Full Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-custom"
                                placeholder="e.g. Russel Vincent Cuevas" required value="{{ old('name') }}">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold fs-7 mb-1 text-dark">Student ID Number <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="student_id_number"
                                    class="form-control form-control-custom" placeholder="e.g. 2420580" required
                                    value="{{ old('student_id_number') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold fs-7 mb-1 text-dark">Phone Number</label>
                                <input type="text" name="phone" class="form-control form-control-custom"
                                    placeholder="e.g. 09189876543" value="{{ old('phone') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold fs-7 mb-1 text-dark">UB Student Email <span
                                    class="text-danger">*</span></label>
                            <input type="email" name="email" id="regEmailInput"
                                class="form-control form-control-custom" placeholder="your.name@ub.edu.ph" required
                                value="{{ old('email') }}" pattern="^[a-zA-Z0-9._%+-]+@ub\.edu\.ph$">
                            <small class="text-muted fs-7 d-block mt-1"><i class="bi bi-info-circle me-1"></i>
                                Registration is restricted to <strong>@ub.edu.ph</strong> emails only.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold fs-7 mb-1 text-dark">Password <span
                                        class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control form-control-custom"
                                    placeholder="At least 8 characters" required minlength="8">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold fs-7 mb-1 text-dark">Confirm Password <span
                                        class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation"
                                    class="form-control form-control-custom" placeholder="Re-type password" required
                                    minlength="8">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-login-maroon w-100 py-2 fw-bold mt-2">
                            <i class="bi bi-person-check-fill me-1"></i> Register Student Account
                        </button>
                    </form>
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
    @if ($errors->hasBag('registration') || ($errors->any() && (old('student_id_number') || old('name') || $errors->has('name') || $errors->has('student_id_number') || $errors->has('password_confirmation'))))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var regModal = new bootstrap.Modal(document.getElementById('registerModal'));
                regModal.show();
            });
        </script>
    @endif
</body>

</html>
