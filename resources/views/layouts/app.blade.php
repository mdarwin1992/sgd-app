<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>@yield('title') | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description"/>
    <meta content="Coderthemes" name="author"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{ asset ('assets/images/favicon.ico')}}">

    <link rel="stylesheet" href="{{ asset('assets/vendor/FontAwesome-pro/css/all.min.css')}}">

    <!-- Theme Config Js -->
    <script src="{{ asset ('assets/js/hyper-config.js')}}"></script>

    <!-- App css -->
    <link href="{{ asset ('assets/css/app-saas.min.css')}}" rel="stylesheet" type="text/css" id="app-style"/>

    <!-- Icons css -->
    <link href="{{ asset ('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css"/>

    <!-- Datatables css -->
    <link href="{{ asset ('assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset ('assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css')}}"
          rel="stylesheet" type="text/css"/>

    <!-- SimpleMDE css -->
    <style>
        #loading-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            display: none;
        }

        .loading-spinner-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .loading-spinner {
            width: 120px;
            height: 120px;
            border: 16px solid #f3f3f3;
            border-top: 16px solid #3498db;
            border-radius: 50%;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        .table-size {
            font-size: 13px !important;
        }
    </style>
</head>

<body>
<div id="preloader">
    <div id="status">
        <div class="bouncing-loader">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
</div>
<div class="wrapper">
    <!-- ========== Topbar Start ========== -->
    @include('layouts.header')
    <!-- ========== Topbar End ========== -->

    <!-- ========== Left Sidebar Start ========== -->
    @include('layouts.menu')
    <!-- ========== Left Sidebar End ========== -->

    <!-- ============================================================== -->
    <!-- Start Page Content here -->
    <!-- ============================================================== -->

    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <!-- Este es donde se renderizarán nuestros componentes -->
                @yield('content')

            </div> <!-- container -->
        </div> <!-- content -->

        <!-- Footer Start -->
        @include('layouts.footer')
        <!-- end Footer -->
    </div>
</div>

<!-- end auth-fluid-->
<!-- Vendor js -->
<script src="{{ asset ('assets/js/vendor.min.js')}}"></script>

<!-- App js -->
<script src="{{ asset ('assets/js/app.min.js')}}"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Datatables js -->
<script src="{{asset('assets/vendor/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js')}}"></script>

<!-- JS Libraies -->
<script src="{{asset('assets/vendor/jquery-validation/dist/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/vendor/jquery-validation/dist/additional-methods.min.js')}}"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/locales/es.js"></script>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css" rel="stylesheet">

<script type="module" src="{{ asset ('services/RouterSif/RouteService.js')}}"></script>
@yield('scripts')
</body>

</html>
