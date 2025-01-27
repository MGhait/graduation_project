<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4;">
<!-- Centering Wrapper -->
<table align="center" width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto;">
    <tr>
        <td align="center">
            <!-- Email Container -->
            <table width="100%" cellpadding="0" cellspacing="0" style="background: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); overflow: hidden;">
                <tr>
                    <td style="background: linear-gradient(276.1deg, #007aff 0.31%, #47b7e8 100%); color: #ffffff; text-align: center; padding: 20px 15px;">
                        <h1 style="margin: 0; font-size: 24px; font-weight: bold; display: flex; align-items: center; justify-content: center; gap: 10px;">
                            <img src="{{ url('images/logo-1.png') }}" alt="Gadget Guru Logo" style="vertical-align: middle; width: 40px; height: 40px; border-radius: 50%; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                            Gadget Guru
                        </h1>
                        <p style="margin-top: 5px; font-size: 16px;">Email Verification</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 20px; text-align: center;">
                        <h2 style="font-size: 20px; margin-bottom: 15px; color: #333333;">Welcome {{ $name }} to Gadget Guru</h2>
                        <p style="font-size: 14px; line-height: 1.6; color: #555555; margin-bottom: 20px;">
                            We received a request to reset the password for your Gadget Guru account.
                            Please verify your email address by clicking the button below or entering the OTP code provided.
                        </p>
                        <a href="{{ $url }}" style="display: inline-block; background: #007aff; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 5px; font-size: 16px; margin: 20px auto; transition: background-color 0.3s ease;">
                            Verify your email
                        </a>
                        <p style="font-size: 13px; color: #888888; margin-bottom: 10px;">
                            If you didn’t request this, you can safely ignore this email.
                        </p>
                        <p style="font-size: 14px; color: #333333;">Thanks!</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
