<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="QA Reporting System for tracking testing activities">
    <meta name="author" content="Tester Report Team">

    <title>{{ config('app.name', 'Reporter') }}</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/multi-select-tag.css') }}">

    <!-- App CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/style.css'])

    <!-- Additional CSS -->
    @stack('styles')
</head>

<body class="font-sans antialiased">
    <div class="page">
        @include('tabler.sidebar')

        <div class="min-h-screen bg-gray-100 page-wrapper">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
                @include('modals.loading')
            </main>
        </div>
    </div>

    <!-- Core JS -->
    <script src="{{ asset('js/jQuery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrip5.3.3.min.js') }}"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>

    <!-- Third-party JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@3.1.0/dist/js/multi-select-tag.js"></script>

    <!-- Utility JS -->
    <script src="{{ asset('js/utils.js') }}"></script>

    <!-- Loading functionality -->
    <script>
        function showLoading(isDataTable = false) {
            $('#loadingModal').modal('show');

            if (isDataTable) {
                $("#loadingSvg").addClass('d-none');
                $(".loader").removeClass('d-none');
            } else {
                $("#loadingSvg").removeClass('d-none');
                $(".loader").addClass('d-none');
            }
        }

        function hideLoading() {
            $('#loadingModal').modal('hide');
        }
    </script>

    <!-- Additional JS -->
    @stack('scripts')
</body>

</html>
