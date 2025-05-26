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

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/multi-select-tag.css') }}">

    <!-- Theme CSS -->
    @vite(['resources/css/datatables-custom.css', 'resources/css/sidebar.css', 'resources/css/navbar.css', 'resources/css/footer.css', 'resources/css/theme.css'])
    <!-- App CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/style.css'])

    <!-- Additional CSS -->
    @stack('styles')
</head>

<body class="font-sans antialiased">
    <!-- Sidebar Backdrop (Mobile) -->
    <div class="sidebar-backdrop"></div>

    <div class="page">
        <!-- Sidebar -->
        @include('tabler.sidebar')

        <div class="page-wrapper">
            <!-- Navbar -->
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
            <header class="bg-white shadow">
                <div class="container-fluid py-3 px-4">
                    {{ $header }}
                </div>
            </header>
            @endif

            <!-- Page Content -->
            <main>
                <div class="container-fluid py-3 px-4">
                    {{ $slot }}
                </div>
                @include('modals.loading')
            </main>

            <!-- Footer -->
            <footer class="footer">
                <div class="container-fluid py-3 px-4">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-0 text-muted">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="mb-0 text-muted">Version 1.0.0</p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Core JS -->
    <script src="{{ asset('js/jQuery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrip5.3.3.min.js') }}"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>

    <!-- Third-party JS -->
    <script src="{{ asset('js/sweatalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/multi-select-tags.js') }}"></script>

    <!-- Theme JS -->
    @vite(['resources/js/theme.js', 'resources/js/utils.js'])

    <!-- Loading functionality -->
    <script>
        function showLoading(isDataTable = false) {
            // $('#loadingModal').modal('show');

            // if (isDataTable) {
            //     $("#loadingSvg").addClass('d-none');
            //     $(".loader").removeClass('d-none');
            // } else {
            //     $("#loadingSvg").removeClass('d-none');
            //     $(".loader").addClass('d-none');
            // }
        }

        function hideLoading() {
            $('#loadingModal').modal('hide');
        }
    </script>

    <!-- Additional JS -->
    @stack('scripts')
</body>

</html>
