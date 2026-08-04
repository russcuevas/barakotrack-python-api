<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | UB BarakoTrack</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('logo/favicon.png') }}">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #752738;
            --primary-hover: #5a1e2c;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .reset-card {
            max-width: 440px;
            width: 100%;
            border-radius: 16px;
            background-color: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .form-control-custom {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 0.65rem 1rem;
            font-size: 0.95rem;
        }

        .form-control-custom:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(117, 39, 56, 0.12);
        }

        .btn-maroon {
            background-color: var(--primary-color);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 0.7rem;
            font-weight: 700;
        }

        .btn-maroon:hover {
            background-color: var(--primary-hover);
            color: #ffffff;
        }
    </style>
</head>

<body>

    <div class="reset-card">
        <div class="text-white p-4 text-center" style="background-color: var(--primary-color);">
            <img src="{{ asset('logo/favicon.png') }}" width="44" height="44" class="mb-2" alt="UB Seal">
            <h5 class="fw-bold m-0"><i class="bi bi-shield-lock-fill me-2 text-warning"></i> Reset Password</h5>
            <small class="text-white-50">Set a new password for {{ $user->email }}</small>
        </div>

        <div class="p-4">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show fs-7 py-2 mb-3" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('password.update', $user->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold fs-7">New Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control form-control-custom" placeholder="At least 8 characters" required minlength="8">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold fs-7">Confirm New Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control form-control-custom" placeholder="Re-type new password" required minlength="8">
                </div>

                <button type="submit" class="btn btn-maroon w-100 py-2 fw-bold">
                    <i class="bi bi-check-circle-fill me-1"></i> Update Password
                </button>
            </form>
        </div>
    </div>

</body>

</html>
