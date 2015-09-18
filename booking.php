<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
?>
<html lang="en" ng-app="app">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

	<title>UoftBaddy</title>
    <!-- STYLES -->
    <!-- build:css lib/css/main.min.css -->
    <link rel="stylesheet" type="text/css" href="/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="/bower_components/rdash-ui/dist/css/rdash.min.css">
    <link rel="stylesheet" type="text/css" href="/css/booking.css">

    <!-- endbuild -->
    <!-- SCRIPTS -->
    <!-- build:js lib/js/main.min.js -->
    <script type="text/javascript" src="/bower_components/angular/angular.min.js"></script>
    <script type="text/javascript" src="/bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js"></script>
    <script type="text/javascript" src="/bower_components/angular-cookies/angular-cookies.min.js"></script>
    <script type="text/javascript" src="/bower_components/angular-ui-router/release/angular-ui-router.min.js"></script>
    <!-- endbuild -->
    <!-- Custom Fonts -->
    <link href="bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- jQuery -->
    <script src="/bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Calendar-->
    <link rel="stylesheet" href="bower_components/fullcalendar/dist/fullcalendar.css"/>
    <!--Calendar-->
    <script type="text/javascript" src="bower_components/moment/min/moment.min.js"></script>
    <script type="text/javascript" src="bower_components/angular-ui-calendar/src/calendar.js"></script>
    <script type="text/javascript" src="bower_components/fullcalendar/dist/fullcalendar.min.js"></script>
    <script type="text/javascript" src="bower_components/fullcalendar/dist/gcal.js"></script>
    <script src="bower_components/ngDialog/js/ngDialog.js"></script>



    <!-- Bootstrap Core JavaScript -->
    <script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>


    <!-- Custom Scripts -->
    <script type="text/javascript" src="/angular/index/index.js"></script>
</head>
<body ng-controller="controller" ng-init="init('<?php echo $user->user_id;?>'">
    <div id="page-wrapper" ng-class="{'open': toggle}" ng-cloak>

    <?php Renderer::get_sidebar();?>
        <div id="content-wrapper">
            <div class="page-content">

                <!-- Header Bar -->
                <div class="row header">
                    <div class="col-xs-12">
                        <div class="user pull-right">
                            <div class="item dropdown">
                                <a href="#" class="dropdown-toggle">
                                    <img ng-src="{{user.avatar}}">
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li class="dropdown-header">
                                        {{user.email}}
                                    </li>
                                    <li class="divider"></li>
                                    <li class="link">
                                        <a ng-href="profile.php?id={{user.user_id}}">
                                            Profile
                                        </a>
                                    </li>
                                    <li class="link">
                                        <a href="#">
                                            Menu Item
                                        </a>
                                    </li>
                                    <li class="link">
                                        <a href="#">
                                            Menu Item
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                    <li class="link">
                                        <a href="#">
                                            Logout
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="item dropdown">
                             <a href="#" class="dropdown-toggle">
                                    <i class="fa fa-bell-o"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li class="dropdown-header">
                                        Notifications
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="#">Server Down!</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="meta">
                            <div class="page">sy
                                Dashboard
                            </div>
                            <div class="breadcrumb-links">
                                Home / Dashboard
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Header Bar -->

                <!-- Main Content -->
                <div ui-view>
                    <div class="row">
                        <div class="col-lg-6">
                            <rd-widget>
                                <rd-widget-header title="Booking Courts">
                                </rd-widget-header>
                                <rd-widget-body>
                                    <div class="title">
                                        Booking Courts at the Univerisity of Toronto
                                    </div>
                                    <div class="message">
                                        Booking badminton courts at the Univeristy of Toronto normally requires one day in prior if you are booking a popular time slot and courts often quickly fill up, leaving either only 7am available for the next day or the day of. 
                                        <br/><br/>
                                        The current main location to play badminton is the <strong>Upper Gym</strong> with around 3 courts and located at the <strong>Faculty of Kinesiology and Physical Education</strong>. 
                                        <br/><br/>

                                        Instructions on how to book courts is available at the University of Toronto site <a href="http://physical.utoronto.ca/FitnessAndRecreation/Drop_In_Programs/Racquet_Sport_Bookings.aspx">here</a>. You can request to book a court by calling <a href="#">416-978-3436</a> and can skip the dialog at pressing "0" where you will then be redirected to an assistant requesting your University of Toronto Student Number and name.
                                    </div>
                                </rd-widget-body>
                            </rd-widget>
                        </div>
                        <div class="col-lg-6">
                            <rd-widget>
                                <rd-widget-header title="Info">
                                </rd-widget-header>
                                <rd-widget-body>
                                    <div class="title">
                                        Faculty of Kinesiology and Physical Education
                                    </div>
                                    <div class="message">
                                        <p class="text-center">
                                            <strong>Address</strong>
                                            55 Harbord Street, Toronto ON, M5S 2W6
                                        </p>
                                    </div>
                                    <div class="message">
                                        <p class="text-center">
                                            <strong>Office Hours:</strong>
                                            7:00 a.m - 10:00 p.m (varies)
                                        </p>
                                    </div>
                                    <div class="message">
                                        <a href="https://www.google.ca/maps/place/Athletic+Centre/@43.6628293,-79.4007772,17z/data=!4m2!3m1!1s0x882b34be4a6157d5:0x2b0ffafd1f73bfb5!6m1!1e1">
                                            <img src="ui/maps.png" class="img-responsive">
                                        </a>
                                    </div>
                                    <div class="message">
                                        <p class="text-center">
                                            <strong>Phone No.</strong>
                                            416-987-3436
                                        </p>
                                    </div>
                                    <div class="message">
                                        <p class="text-center">
                                            <strong>Primary Location:</strong>
                                            Upper Gym
                                        </p>
                                    </div>
                                    <div class="message">
                                        *Courts normally fill up well before 10am (that's only 3 hours after office opens for booking). For best chances to get the court you want, you should try to book as early as possible (the office opens at 7am)
                                    </div>
                                </rd-widget-body>
                            </rd-widget>
                        <div class="col-lg-3 col-md-6 col-xs-12">
                            <rd-widget>
                                <rd-widget-body>
<!--                                     <div class="widget-icon green pull-left">
                                        <i class="fa fa-users"></i>
                                    </div> -->
                                    <div class="title">{{data.TopBar.confirmed_bookings_today}}</div>
                                    <div class="comment">Confirmed Bookings Today</div>
                                </rd-widget-body>
                            </rd-widget>
                        </div>
                        <div class="col-lg-3 col-md-6 col-xs-12">
                            <rd-widget>
                                <rd-widget-body>
<!--                                     <div class="widget-icon green pull-left">
                                        <i class="fa fa-users"></i>
                                    </div> -->
                                    <div class="title">{{data.TopBar.confirmed_bookings_tomorrow}}</div>
                                    <div class="comment">Confirmed Bookings Tomorrow
                                    <small>{{data.topBar.total_space_tomorrow}} space left (according to user specifications)</small>
                                    </div>
                                </rd-widget-body>
                            </rd-widget>
                        </div>
                        <div class="col-lg-3 col-md-6 col-xs-12">
                            <rd-widget>
                                <rd-widget-body>
<!--                                     <div class="widget-icon green pull-left">
                                        <i class="fa fa-users"></i>
                                    </div> -->
                                    <div class="title">{{data.TopBar.looking_to_play_this_week}}</div>
                                    <div class="comment">Looking To Play This Week</div>
                                </rd-widget-body>
                            </rd-widget>
                        </div>
<!--                         <div class="col-lg-3 col-md-6 col-xs-12">
                            <rd-widget>
                                <rd-widget-body>
                                    <div class="widget-icon green pull-left">
                                        <i class="fa fa-users"></i>
                                    </div>
                                    <div class="title">{{data.TopBar.confirmed_bookings_today}}</div>
                                    <div class="comment">Confirmed Bookings Todat</div>
                                </rd-widget-body>
                            </rd-widget>
                        </div> -->
                    </div>
                    <div class="row">
                        <div class="col-lg-8">
                            <div ng-if="eventSources">
                              <button class="btn btn-info btn-sm" ng-click="changeView('agendaDay')">Day</button>
                              <button class="btn btn-info btn-sm" ng-click="changeView('agendaWeek')">Week</button>
                              <button class="btn btn-info btn-sm" ng-click="changeView('month')">Month</button>
                              <div ui-calendar ng-model="eventSources" calendar="main"></div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                              <h4>Inline</h4>
    <div style="display:inline-block; min-height:290px;">
      <datepicker ng-model="dt" min-date="minDate" show-weeks="true" class="well well-sm" custom-class="getDayClass(date, mode)"></datepicker>
    </div>
    </div>
                    </div>
                </div>

            </div><!-- End Page Content -->
        </div><!-- End Content Wrapper -->
    </div><!-- End Page Wrapper -->
</body>
</html>