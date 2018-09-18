<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title') :: Fresh Tariffs Panel</title>
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel='stylesheet' href='/css/bootstrap.min.css'>
    <link rel='stylesheet' href='/css/style.css'>
    <link rel='stylesheet' href='/css/custom.css'>
    <link rel="stylesheet" href="/css/select2.min.css">
    <link rel="stylesheet" href="/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" />

    <!--[if lte IE 9]><!-->
    <!--<![endif]-->
</head>
<body>

<div class="site is-loaded is-scroll">
    <div class="site-canvas">
        <header class="site-header">
            <nav class="navbar navbar-inverse navbar-static-top">
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <div class="navbar-brand-wrap">
                            <a class="navbar-brand" href="{{ URL::to('/') }}">
                                <img src="/img/site-header-logo.png" alt="">
                            </a>
                        </div>
                    </div> <!-- .navbar-header -->

                    <div class="collapse navbar-collapse" id="navbar-collapse">
                        <ul class="nav navbar-nav">
                        @if(BaseController::isStaff())
                            <li><a href="{{ URL::to('/') }}">Home</a></li>
                            <li><a href="{{ URL::to('/staff/customers') }}">Customers</a></li>
                            <li><a href="{{ URL::to('/staff/pricelists') }}">Price Lists</a></li>
                            <li><a href="{{ URL::to('/staff/subscription') }}">Subscription</a></li>
                            <li><a href="{{ URL::to('/staff/settings') }}">Settings</a></li>
                            <li><a href="#">Help</a></li>
                        @endif
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            @if(BaseController::isLoggedIn())
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">{{ $user['username'] }} <span class="caret"></span></a>
                                    <ul class="dropdown-menu" role="menu">
                                        @if(BaseController::isAdmin())
                                            <li><a href="{{ URL::to('/admin') }}">Admin panel</a></li>
                                            <li class="divider"></li>
                                        @endif
                                        <li><a href="{{ URL::to('/users/logout') }}?_token={{ csrf_token() }}">Logout</a></li>
                                    </ul>
                                </li>
                            @else
                                <li><a href="{{ URL::to('/users/login') }}">Login</a></li>
                            @endif
                        </ul>
                    </div> <!-- .navbar-collapse -->
                </div>
            </nav>
        </header> <!-- .site-header -->
        <main class="site-main">
            <div class="container">
            @yield('content')
            </div>
        </main> <!-- .site-main -->
    </div>
</div>

<script type="text/javascript" src="/js/jquery.min.js"></script>
<script type="text/javascript" src="/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/bower_components/moment/min/moment.min.js"></script>
<script type="text/javascript" src="/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

<script type="text/javascript">
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    $(function () {
        $('.datepicker').datetimepicker({
            format: 'DD.MM.YYYY'
        });
    });
</script>
@yield('bottom_scripts')
</body>
</html>