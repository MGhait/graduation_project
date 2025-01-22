<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        /* Logo styling */
        .logo {
            vertical-align: middle;
            width: 40px; /* Adjust as needed */
            height: 40px; /* Adjust as needed */
            margin-right: 10px;
            border-radius: 50%; /* Optional: makes the logo round */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Optional: adds a subtle shadow */
        }

        /* Ensure the header text aligns properly with the logo */
        .header h1 {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px; /* Space between the logo and the text */
            font-size: 24px;
        }
        /* Reset styles for better cross-browser compatibility */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Arial", sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
        }

        .email-container {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 90%;
            overflow: hidden;
            animation: fadeIn 1s ease-out;
        }

        .header {
            background: linear-gradient(276.1deg, #007aff 0.31%, #47b7e8 100%);
            color: #ffffff;
            text-align: center;
            padding: 20px 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            animation: fadeInDown 1s ease-out;
        }

        .header p {
            margin-top: 5px;
            font-size: 16px;
            animation: fadeInDown 1.2s ease-out;
        }

        .content {
            padding: 20px;
            text-align: center;
            animation: slideUp 1.2s ease-out;
        }

        .content h2 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #333333;
        }

        .content p {
            font-size: 14px;
            line-height: 1.6;
            color: #555555;
            margin-bottom: 20px;
        }

        .otp {
            background: #f4f9ff;
            border: 1px dashed #007aff;
            padding: 12px 24px;
            border-radius: 5px;
            display: block;
            font-size: 24px;
            color: #007aff;
            font-weight: bold;
            margin: 20px auto;
            width: 80%;
            animation: bounce 1.5s infinite;
        }

        .verify-btn {
            display: block;
            background: #007aff;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 5px;
            font-size: 16px;
            margin: 20px auto;
            width: 80%;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .verify-btn:hover {
            background: #007acf;
            transform: scale(1.05); /* Grow on hover */
        }

        .note {
            font-size: 13px;
            color: #888888;
            margin-bottom: 10px;
        }

        .content p:last-child {
            font-size: 14px;
            color: #333333;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes bounce {
            0%,
            100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header h1 {
                font-size: 20px;
            }

            .content h2 {
                font-size: 18px;
            }

            .otp {
                font-size: 20px;
                padding: 10px 20px;
            }

            .verify-btn {
                font-size: 14px;
                padding: 10px 20px;
            }
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 18px;
            }

            .content h2 {
                font-size: 16px;
            }

            .otp {
                font-size: 18px;
                padding: 8px 16px;
                width: 90%;
            }

            .verify-btn {
                font-size: 14px;
                padding: 8px 16px;
                width: 90%;
            }
        }

    </style>
</head>
<body>
<div class="email-container">
    <div class="header">
        <h1 class="fade-in">
            <img src="{{asset('images/logo-1.png')}}" alt="Gadget Guru Logo" class="logo">
            Gadget Guru
        </h1>
        <p class="fade-in delay-1">Email Verification</p>
    </div>
    <div class="content slide-up">
        <h2>Welcome {{ $name }} to Gadget Guru</h2>
        <p>
            We received a request to reset the password for your Gadget Guru account.
            Note that this OTP code will expire after 20 minutes.
        </p>
        <div class="otp bounce">
            <span>{{$otp}}</span>
        </div>
        <p class="note">
            If you didn’t request this, you can safely ignore this email.
        </p>
        <p>Thanks!</p>
    </div>
</div>
</body>
</html>
