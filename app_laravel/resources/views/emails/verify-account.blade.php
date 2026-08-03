<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verify Your UB BarakoTrack Account</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f6f9; color: #333333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 30px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .header { background-color: #752738; color: #ffffff; padding: 25px; text-align: center; }
        .header h2 { margin: 0; font-size: 22px; font-weight: bold; }
        .content { padding: 30px; line-height: 1.6; }
        .btn-verify { display: inline-block; background-color: #752738; color: #ffffff !important; text-decoration: none; padding: 14px 28px; border-radius: 8px; font-weight: bold; font-size: 15px; margin: 20px 0; }
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
            <p>Thank you for registering your student account on <strong>UB BarakoTrack</strong> (Student Affairs Office Lost & Found System).</p>
            <p>To activate your account and start logging in, please confirm your university email address by clicking the button below:</p>
            
            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="btn-verify">Verify & Activate Account</a>
            </div>

            <p style="font-size: 13px; color: #6c757d;">If the button above doesn't work, copy and paste this link into your web browser:</p>
            <p style="font-size: 12px; word-break: break-all; color: #752738;">{{ $verificationUrl }}</p>

            <p>If you did not register for a BarakoTrack account, please ignore this email.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} University of Batangas Lipa Campus - Student Affairs Office (SAO). All rights reserved.
        </div>
    </div>
</body>
</html>
