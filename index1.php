<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$user = User::get_current_user();   
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
<body ng-controller="controller" ng-init="init('<?php echo $user->user_id;?>')">
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
                                <?php Renderer::get_user_dropdown();?>
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
                                        <a href="#">No notifications</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="meta">
                            <div class="page">
                                Home
                            </div>
                            <div class="breadcrumb-links">
                                Home
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Header Bar -->

                <!-- Main Content -->
                <div ui-view>
                    <div class="row">
                        <div class="col-lg-12">
                            <rd-widget>
                                <rd-widget-header title="UoftBaddy">
                                </rd-widget-header>
                                <rd-widget-body>
                                    <div class="message">
                                        UoftBaddy is an open source service for students at the University of Toronto, dedicated at helping students easily find times and players to play badminton with. You must be a University of Toronto student to sign up for the service. 
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