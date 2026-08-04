<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Your UB BarakoTrack Password</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f6f9; color: #333333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 30px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .header { background-color: #752738; color: #ffffff; padding: 25px; text-align: center; }
        .header h2 { margin: 0; font-size: 22px; font-weight: bold; }
        .content { padding: 30px; line-height: 1.6; }
        .btn-reset { display: inline-block; background-color: #752738; color: #ffffff !important; text-decoration: none; padding: 14px 28px; border-radius: 8px; font-weight: bold; font-size: 15px; margin: 20px 0; }
        .footer { background-color: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #6c757d; border-top: 1px solid #e9ecef; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>University of Batangas - BarakoTrack</h2>
        </div>
        <div class="content">
            <p>Hello <strong>{{ $user->name }}</strong>,</p>
            <p>We received a request to reset the password for your <strong>UB BarakoTrack</strong> account ({{ $user->email }}).</p>
            <p>Click the button below to open the password reset page and set your new password:</p>
            
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="btn-reset">Reset My Password</a>
            </div>

            <p style="font-size: 13px; color: #6c757d;">This password reset link will expire in 60 minutes. If the button doesn't work, copy and paste this link into your browser:</p>
            <p style="font-size: 12px; word-break: break-all; color: #752738;">{{ $resetUrl }}</p>

            <p>If you did not request a password reset, no further action is required and your account remains safe.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} University of Batangas Lipa Campus - Student Affairs Office (SAO). All rights reserved.
        </div>
    </div>
</body>
</html>
