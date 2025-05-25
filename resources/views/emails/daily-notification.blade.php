<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>Daily Task Update Reminder - Tester Report</title>
    <!--[if mso]>
    <style type="text/css">
        .f-fallback {
            font-family: Arial, sans-serif;
        }
    </style>
    <![endif]-->
    <style>
        /* Base styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #ddd;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .content {
            padding: 30px 20px;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }
        h1 {
            color: #333;
            margin-top: 0;
        }
        .btn {
            display: inline-block;
            background-color: #007bff;
            color: white !important;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 4px;
            margin-top: 20px;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #0069d9;
        }
        .unsubscribe {
            color: #999;
            text-decoration: underline;
            font-size: 11px;
        }
        @media only screen and (max-width: 620px) {
            .container {
                width: 100% !important;
                padding: 10px !important;
            }
            .content {
                padding: 20px 10px !important;
            }
        }
        .text-white{
            color: white !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Tester Report</h1>
        </div>

        <div class="content">
            <p>Hello {{ $userName ?? "Sohail Khan" }},</p>

            <p>This is your daily reminder to update your tasks in the Tester Report system.</p>

            <p>Please take a moment to log your daily activities and progress. Keeping your reports up-to-date helps the team track project status and ensures everyone stays informed.</p>

            <p style="text-align: center;">
                <a href="{{ $reportingUrl ?? route('reporting') }}" class="btn text-white">Update My Reports</a>
                {{-- <a href="http://localhost:8000/reporting" class="btn text-white">Update My Reports</a> --}}
            </p>

            <p>Thank you for your contribution to the team's success!</p>

            <p>Best regards,<br>The Tester Report Team</p>
        </div>

        <div class="footer">
            <p>&copy; {{ $currentYear ?? date('Y') }} Tester Report. All rights reserved.</p>
            <p><a href="{{ $unsubscribeUrl ?? $this->user->id }}" class="unsubscribe">Unsubscribe</a> from these notifications.</p>
        </div>
    </div>
</body>
</html>
