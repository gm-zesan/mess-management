<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Font Awesome Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

        <!-- Toastr CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

        <!-- DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/v/dt/dt-2.3.7/datatables.min.css">

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <!-- DataTables JS -->
        <script src="https://cdn.datatables.net/v/dt/dt-2.3.7/datatables.min.js" defer></script>

        <!-- Toastr JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" defer></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-white">
            @if(Auth::check())
                @include('layouts.navigation')
            @endif

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="w-full py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="w-full px-4">
                <div class="bg-gray-100 sm:px-4 lg:px-8 py-4 mb-6 rounded-3xl border border-gray-200 overflow-y-auto" style="height: calc(100vh - 80px);">
                    @yield('content')
                </div>
                
            </main>
        </div>

        <!-- Session Messages Notification Script -->
        <script>
            var SITEURL = "{{ URL::to('') }}";
            var ASSET_URL = "{{ config('app.asset_url') }}/";
            
            $(document).ready(function() {
                // Setup CSRF token for all AJAX requests
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                @if (session('success'))
                    toastr.success('{{ session('success') }}');
                @endif

                @if (session('error'))
                    toastr.error('{{ session('error') }}');
                @endif

                @if (session('warning'))
                    toastr.warning('{{ session('warning') }}');
                @endif

                @if (session('info'))
                    toastr.info('{{ session('info') }}');
                @endif

                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        toastr.error('{{ $error }}');
                    @endforeach
                @endif
            });
        </script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js" defer></script>
        @stack('custom-scripts')
    </body>
</html>
