<!doctype html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"

      <?php if (str_replace('_', '-', app()->getLocale()) == 'ar' || @$_COOKIE['is_rtl'] == 'true') { ?> dir="rtl" <?php } ?>>

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->

    <meta name="csrf-token" content="{{ csrf_token() }}">

<!-- <title>{{ config('app.name', 'Laravel') }}</title> -->

    <title id="app_name"><?php echo @$_COOKIE['meta_title']; ?></title>

    <link rel="icon" id="favicon" type="image/x-icon"

          href="<?php echo str_replace('images/', 'images%2F', @$_COOKIE['favicon']); ?>">

    <!-- Fonts -->

    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->

    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <?php if (str_replace('_', '-', app()->getLocale()) == 'ar' || @$_COOKIE['is_rtl'] == 'true') { ?>

    <link href="{{asset('assets/plugins/bootstrap/css/bootstrap-rtl.min.css')}}" rel="stylesheet">

    <?php } ?>

    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <?php if (str_replace('_', '-', app()->getLocale()) == 'ar' || @$_COOKIE['is_rtl'] == 'true') { ?>

    <link href="{{asset('css/style_rtl.css')}}" rel="stylesheet">

    <?php } ?>

    <link href="{{ asset('css/icons/font-awesome/css/font-awesome.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/plugins/toast-master/css/jquery.toast.css')}}" rel="stylesheet">

    <link href="{{ asset('css/colors/green.css') }}" rel="stylesheet">

    <link href="{{ asset('css/chosen.css') }}" rel="stylesheet">

    <link href="{{ asset('css/bootstrap-tagsinput.css') }}" rel="stylesheet">



    <link href="{{ asset('assets/plugins/summernote/summernote-bs4.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">

    <style>
        :root {
            --admin-primary: #0f3524;
            --admin-accent: #ffca28;
            --admin-text: #c8e6c9;
            --hover-shadow: 0 12px 24px rgba(0,0,0,0.08);
            --card-shadow: 0 4px 12px rgba(0,0,0,0.03);
            --primary: #0f3524;
            --success: #2e7d32;
            --warning: #ffca28;
        }
        body { background: #f4f7f5 !important; }
        .topbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 4px 12px rgba(0,0,0,0.02) !important;
        }
        .left-sidebar { background: var(--admin-primary) !important; }
        .sidebar-nav ul li a {
            color: var(--admin-text) !important;
            transition: all 0.2s ease;
        }
        .sidebar-nav ul li a:hover,
        .sidebar-nav ul li a.active {
            color: #0d2b1f !important;
            background: var(--admin-accent) !important;
            border-radius: 8px; margin: 4px 10px;
            transform: translateX(4px);
            padding-left: 10px !important;
        }
        .sidebar-nav > ul > li > a i { color: inherit !important; }
        
        .card {
            border-radius: 12px !important;
            box-shadow: var(--card-shadow) !important;
            border: none !important;
        }
        .hover-card { transition: all 0.3s ease !important; border-radius: 12px; }
        .hover-card:hover {
            transform: translateY(-5px) !important;
            box-shadow: var(--hover-shadow) !important;
        }
        
        /* Dashboard specific modern touches */
        .card-box {
            border-radius: 12px !important;
            box-shadow: var(--card-shadow) !important;
            transition: all 0.3s ease !important;
            background: linear-gradient(135deg, #0f3524 0%, #175036 100%) !important;
            color: #fff !important;
            border: none !important;
            padding: 20px !important;
        }
        .card-box:hover {
            transform: translateY(-5px) !important;
            box-shadow: var(--hover-shadow) !important;
        }
        .card-box h5 { color: #c8e6c9 !important; font-weight: 600; margin-bottom: 10px;}
        .card-box h2 { color: #ffca28 !important; font-weight: bold; }
        .card-box i { color: rgba(255,202,40,0.8) !important; }
        
        .order-status {
            border-radius: 12px !important;
            background: #fff !important;
            box-shadow: var(--card-shadow) !important;
            transition: all 0.3s ease !important;
            border: 1px solid rgba(0,0,0,0.02) !important;
            padding: 15px !important;
        }
        .order-status:hover {
            transform: translateY(-5px) !important;
            box-shadow: var(--hover-shadow) !important;
            border-color: var(--admin-accent) !important;
        }
        .order-status .data i { color: #0f3524 !important; }
        .order-status .status { color: #444 !important; font-weight: 600; margin-top: 5px; }
        .order-status .count {
            background: #ffca28 !important; color: #0f3524 !important;
            font-weight: bold !important; border-radius: 20px !important;
            padding: 4px 12px !important;
            top: 15px !important;
            right: 15px !important;
        }

        /* ── Global Admin Sub-page Overrides ──────────────────────────── */
        /* Typography */
        .page-titles {
            background: #fff !important; border-radius: 12px !important;
            padding: 15px 25px !important; margin: 20px 0 !important;
            box-shadow: var(--card-shadow) !important;
        }
        .text-themecolor { color: var(--admin-primary) !important; font-weight: 700 !important; }

        /* Buttons (Pagination and Actions) */
        .btn-primary, .btn-info, .btn-success {
            background-color: var(--success) !important; border-color: var(--success) !important; color: #fff !important;
            border-radius: 6px !important; transition: all 0.2s;
        }
        .btn-primary:hover, .btn-info:hover, .btn-success:hover {
            background-color: var(--primary) !important; border-color: var(--primary) !important;
            transform: translateY(-2px); box-shadow: 0 4px 8px rgba(46,125,50,0.2) !important;
        }
        .btn-warning {
            background-color: var(--warning) !important; border-color: var(--warning) !important; color: #000 !important;
        }
        .page-item.active .page-link {
            background-color: var(--success) !important; border-color: var(--success) !important; color: #fff !important;
        }

        /* Tabs */
        .nav-tabs .nav-link { color: #666 !important; font-weight: 600; border: none !important; padding: 12px 20px !important; }
        .nav-tabs .nav-link:hover { color: var(--success) !important; background: transparent !important; }
        .nav-tabs .nav-link.active {
            color: var(--success) !important;
            background: transparent !important;
            border-bottom: 3px solid var(--warning) !important;
        }

        /* Datatables / Tables */
        table.dataTable thead th, table.table thead th {
            background-color: #e8f5e9 !important;
            color: var(--primary) !important;
            border-bottom: 2px solid #c8e6c9 !important;
            font-weight: 700 !important;
        }
        table.dataTable.no-footer { border-bottom: 1px solid #c8e6c9 !important; }
        table.dataTable tbody tr:hover { background-color: #f1f8f2 !important; }

    </style>
</head>

<body>



<div id="app" class="fix-header fix-sidebar card-no-border">

    <div id="main-wrapper">



        <header class="topbar">



            <nav class="navbar top-navbar navbar-expand-md navbar-light">

                @include('layouts.header')

            </nav>



        </header>



        <aside class="left-sidebar">



            <!-- Sidebar scroll-->



            <div class="scroll-sidebar">



                @include('layouts.menu')



            </div>



            <!-- End Sidebar scroll-->



        </aside>



    </div>





    <main class="py-4">

        @yield('content')

    </main>

</div>





<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script src="{{ asset('assets/plugins/bootstrap/js/popper.min.js') }}"></script>

<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>

<script src="{{ asset('js/jquery.slimscroll.js') }}"></script>

<script src="{{ asset('js/waves.js') }}"></script>

<script src="{{ asset('js/sidebarmenu.js') }}"></script>

<script src="{{ asset('assets/plugins/sticky-kit-master/dist/sticky-kit.min.js') }}"></script>

<script src="{{ asset('assets/plugins/sparkline/jquery.sparkline.min.js')}}"></script>

<script src="{{ asset('js/custom.min.js') }}"></script>

<script src="{{ asset('assets/plugins/summernote/summernote-bs4.js')}}"></script>



<script src="{{ asset('js/jquery.resizeImg.js') }}"></script>

<script src="{{ asset('js/mobileBUGFix.mini.js') }}"></script>







<script type="text/javascript">

    jQuery(window).scroll(function () {

        var scroll = jQuery(window).scrollTop();

        if (scroll <= 60) {

            jQuery("body").removeClass("sticky");

        } else {

            jQuery("body").addClass("sticky");

        }

    });



</script>

<!-- Firebase has been removed from the application -->

<script src="{{ asset('js/chosen.jquery.js') }}"></script>

<script src="{{ asset('js/bootstrap-tagsinput.js') }}"></script>

<script src="{{ asset('js/crypto-js.js') }}"></script>

<script src="{{ asset('js/jquery.cookie.js') }}"></script>

<script src="{{ asset('js/jquery.validate.js') }}"></script>

<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script type="text/javascript"

        src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>



<script src="{{ asset('js/jquery.masking.js') }}"></script>



<script type="text/javascript">
    // Firebase has been removed from the application
    // Global settings, languages, and notifications require backend migration

    var url = "{{ route('changeLang') }}";

    $(".changeLang").change(function () {
        window.location.href = url + "?lang=" + $(this).val();
    });

    function setCookie(cname, cvalue, exdays) {
        const d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        let expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }



    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    // Firebase has been removed - stub functions for compatibility
    async function sendEmail(url, subject, message, recipients) {
        return true;
    }

    async function sendNotification(fcmToken = '', title, body) {
        return true;
    }

    async function loadGoogleMapsScript() {
        // Firebase geolocation removed - using browser geolocation
        const script = document.createElement('script');
        let googleMapKey = 'AIzaSyD7-lVVm_uo5ydwCqRYZUP9Sy_qHF8Oi8w'; // Fallback key
        script.src = "https://maps.googleapis.com/maps/api/js?key=" + googleMapKey + "&libraries=drawing,geometry,places";
        script.onload = function () {
            navigator.geolocation.getCurrentPosition(GeolocationSuccessCallback, GeolocationErrorCallback);
            if(typeof window['InitializeGodsEyeMap'] === 'function') {
                InitializeGodsEyeMap();
            }
        };
        document.head.appendChild(script);
    }



    const GeolocationSuccessCallback = (position) => {

        if(position.coords != undefined){

            default_latitude = position.coords.latitude

            default_longitude = position.coords.longitude

            setCookie('default_latitude', default_latitude, 365);

            setCookie('default_longitude', default_longitude, 365);

        }

    };



    const GeolocationErrorCallback = (error) => {

        console.log('Error: You denied for your default Geolocation',error.message);

        setCookie('default_latitude', '23.022505', 365);

        setCookie('default_longitude','72.571365', 365);

    };



    loadGoogleMapsScript();



    async function sendNotification(fcmToken = '', title, body) {

        var checkFlag = false;

        // FCM notification endpoint (route not yet defined)
        var sendNotificationUrl = null;

        if (fcmToken !== '' && sendNotificationUrl) {

            await $.ajax({

                type: 'POST',

                url: sendNotificationUrl,

                data: {

                    _token: $('meta[name="csrf-token"]').attr('content'),

                    'fcm': fcmToken,

                    'title': title,

                    'message': body

                },

                success: function (data) {

                    checkFlag = true;

                },

                error: function (error) {

                    checkFlag = true;

                }

            });

        } else {

            checkFlag = true;

        }



        return checkFlag;

    }


    // Firebase notification service removed

</script>



@yield('scripts')



</body>

</html>

