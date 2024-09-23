<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Reporter') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@3.1.0/dist/css/multi-select-tag.css">
    <link rel="stylesheet" href="css/style.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
    <script src="{{ asset('js/jQuery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrip5.3.3.min.js') }}"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@3.1.0/dist/js/multi-select-tag.js"></script>
    <script> 
        function showLoading(isDataTable=false){
            // console.log("Show Loading ");
            
            // $("#loadingModal").modal({
                // backdrop: 'static',  // Prevent closing when clicking outside
                // keyboard: false      // Disable closing with the "Esc" key
            // }).modal('show');
            if(isDataTable){
                $("#loadingSvg").addClass('d-none');
                $(".loader").removeClass('d-none');
            }
            else{
                $("#loadingSvg").removeClass('d-none');
                $(".loader").addClass('d-none');
            }
        }
        function hideLoading(){
            // console.log("hide Loading ");
            
            $('#loadingModal').modal('hide');
        }
    </script>
</body>

</html>
