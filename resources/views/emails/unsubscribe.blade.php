<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Preferences - Tester Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 50px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            border-top-left-radius: 10px !important;
            border-top-right-radius: 10px !important;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Email Notification Preferences</h3>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <p class="lead text-center mb-4">Hello {{ $user->name }},</p>

                        <p>You are currently receiving daily email notifications from the Tester Report system. Would you like to unsubscribe from these notifications?</p>

                        <form action="{{ route('email.preferences.update', $user->id) }}" method="POST" class="text-center mt-4">
                            @csrf
                            <button type="submit" class="btn btn-primary">Unsubscribe from Email Notifications</button>
                        </form>

                        <div class="mt-4 text-center">
                            <p class="text-muted">If you did not request to unsubscribe, you can safely ignore this page.</p>
                        </div>
                    </div>
                    <div class="card-footer text-center text-muted">
                        <small>&copy; {{ date('Y') }} Tester Report. All rights reserved.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
