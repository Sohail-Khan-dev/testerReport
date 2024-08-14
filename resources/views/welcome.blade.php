
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa; /* Use your background color */
        }
        .centered-links {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .custom-link {
            display: block;
            font-weight: 600;
            color: #495057;
            text-decoration: none;
            margin: 5px;
            transition: all 0.3s ease;
        }
        .custom-link:hover {
            color: #212529;
        }
    </style>
</head>

<body class="bg-light">

    <div class="centered-links">
        @auth
        <a href="{{ url('/dashboard') }}" class="custom-link">Dashboard</a>
        @else
        <a href="{{ route('login') }}" class="custom-link">Log in</a>
        @if (Route::has('register'))
        <a href="{{ route('register') }}" class="custom-link">Register</a>
        @endif
        @endauth
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
