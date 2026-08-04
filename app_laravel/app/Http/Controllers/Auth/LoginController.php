<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use App\Mail\VerifyStudentAccountEmail;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();
            return $user->role === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('student.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $user = Auth::user();

            if ($user->status === 'inactive') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Your student account is currently inactive. Please check your @ub.edu.ph email inbox and click the verification link to activate your account.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Welcome to SAO Admin Control Panel!');
            }

            return redirect()->route('student.dashboard')->with('success', 'Welcome to UB Barako Track Student Portal!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    public function register(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'student_id_number' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
                function ($attribute, $value, $fail) {
                    if (!str_ends_with(strtolower($value), '@ub.edu.ph')) {
                        $fail('Registration is restricted to University of Batangas students only. Email must end with @ub.edu.ph.');
                    }
                }
            ],
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.unique' => 'This @ub.edu.ph email address is already registered.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 8 characters long.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator, 'registration')->withInput();
        }

        $validated = $validator->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'student_id_number' => $validated['student_id_number'],
            'phone' => $validated['phone'] ?? null,
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'role' => 'student',
            'status' => 'inactive',
        ]);

        // Generate temporary signed verification URL valid for 24 hours
        $verificationUrl = URL::temporarySignedRoute(
            'verify.account',
            now()->addHours(24),
            ['id' => $user->id]
        );

        // Send verification email via Gmail SMTP
        try {
            Mail::to($user->email)->send(new VerifyStudentAccountEmail($user, $verificationUrl));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('SMTP Verification Email Exception: ' . $e->getMessage());
        }

        return redirect()->route('login')->with('success', "Registration successful! A verification link has been sent to {$user->email}. Please check your inbox and verify your email to activate your account.");
    }

    public function verifyAccount(Request $request, $id)
    {
        if (!$request->hasValidSignature()) {
            return redirect()->route('login')->withErrors(['email' => 'Invalid or expired verification link. Please register again or request support from SAO.']);
        }

        $user = User::findOrFail($id);
        $user->status = 'active';
        $user->email_verified_at = now();
        $user->save();

        return redirect()->route('login')->with('success', 'Your student account has been successfully verified and activated! You can now log into BarakoTrack.');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'forgot_email' => 'required|email|exists:users,email',
        ], [
            'forgot_email.required' => 'Please enter your registered email address.',
            'forgot_email.email' => 'Please enter a valid email address.',
            'forgot_email.exists' => 'No account found with this email address.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator, 'forgot_password')->withInput();
        }

        $user = User::where('email', strtolower($request->forgot_email))->first();

        // Generate temporary signed URL valid for 60 minutes
        $resetUrl = URL::temporarySignedRoute(
            'password.reset',
            now()->addMinutes(60),
            ['id' => $user->id]
        );

        // Send reset email via Gmail SMTP
        try {
            Mail::to($user->email)->send(new \App\Mail\ResetPasswordEmail($user, $resetUrl));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('SMTP Password Reset Exception: ' . $e->getMessage());
        }

        return redirect()->route('login')->with('success', "Password reset link sent to {$user->email}! Please check your email inbox to reset your password.");
    }

    public function showResetPasswordForm(Request $request, $id)
    {
        if (!$request->hasValidSignature()) {
            return redirect()->route('login')->withErrors(['email' => 'Invalid or expired password reset link. Please request a new reset link.']);
        }

        $user = User::findOrFail($id);

        return view('auth.reset_password', compact('user'));
    }

    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 8 characters long.',
        ]);

        $user = User::findOrFail($id);
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        return redirect()->route('login')->with('success', 'Your password has been successfully reset! You can now log into BarakoTrack using your new password.');
    }
}
