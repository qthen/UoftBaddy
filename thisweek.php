<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$user = User::get_current_user();
if ($user instanceof AnonymousUser) {
    header('Location: /fblogin.php');
}
?>
<html lang="en" ng-app="app">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>The UoftBaddy Project - Crowd Calendar</title>
    <!-- STYLES -->
    <!-- build:css lib/css/main.min.css -->
    <link rel="stylesheet" type="text/css" href="/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="/bower_components/rdash-ui/dist/css/rdash.min.css">
    <link rel="stylesheet" type="text/css" href="bower_components/ngDialog/css/ngDialog.css">
    <link rel="stylesheet" type="text/css" href="/css/courts.css">
    <link rel="stylesheet" type="text/css" href="bower_components/ngDialog/css/ngDialog-theme-default.css">
    <!-- endbuild -->
    <!-- SCRIPTS -->
    <!-- build:js lib/js/main.min.js -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.4.5/angular.min.js"></script>
    <script src="bower_components/moment/min/moment.min.js"></script>
    <script type="text/javascript" src="/bower_components/angular-bootstrap/ui-bootstrap-tpls.js"></script>
    <link href="bower_components/angular-bootstrap-calendar/dist/css/angular-bootstrap-calendar.min.css" rel="stylesheet">
    <script src="bower_components/angular-bootstrap-calendar/dist/js/angular-bootstrap-calendar-tpls.min.js"></script>
    <!-- endbuild -->
    <!-- Custom Fonts -->
    <link href="bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- jQuery -->
    <script src="/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="bower_components/ngDialog/js/ngDialog.js"></script>

    <!-- Bootstrap Core JavaScript --> 
    <script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Angular Moment -->
    <script src="bower_components/angular-moment/angular-moment.js"></script>
    <!-- Custom Scripts -->
    <script type="text/javascript" src="/angular/thisweek.js"></script>
</head>
<body ng-controller="controller" ng-init="init('<?php echo $user->user_id;?>')">
    <div id="page-wrapper" ng-class="{'open': toggle}" ng-cloak>

    <?php Renderer::get_sidebar();?>
        <div id="content-wrapper">
            <div class="page-content">

                <!-- Header Bar -->
                <!-- Header Bar -->
                <div class="row header" style="margin:0px;padding:0px;">    
                    <div class="col-xs-12" style="margin-bottom:0px;">
                        <div class="user pull-right">
                            <div class="item dropdown">
                                <a href="#" class="dropdown-toggle">
                                    <img ng-src="{{user.avatar_link}}"> 
                                </a>
                                <?php Renderer::get_user_dropdown();?>
                            </div>
                            <div class="item dropdown">
                                <a href="#" class="dropdown-toggle">
                                    <i class="fa fa-bell-o"><span class="badge" style="font-size:12px;position:absolute;top:10;color:white;background-color:#D9230F;" ng-show="data.newNotifications > 0">{{data.newNotifications}}</span></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right notification">
                                    <li class="dropdown-header">
                                        Notifications
                                        <span class="badge">{{data.newNotifications}}</span>
                                    </li>
                                    <li class="divider"></li>
                                    <li ng-repeat="notification in data.notifications" ng-style="notification.style">
                                        <a href ng-click="propogateRead(notification)">
                                            {{notification.message}}
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                    <li class="dropdown-header">
                                        See All
                                    </li>
                                    </ul>
                                </ul>
                            </div>
                        </div>
                        <div class="meta" style="margin:0px;padding:0px;">   
                            <div class="page">
                                UoftBaddy
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
                                <rd-widget-header title="Info">
                                </rd-widget-header>
                                <rd-widget-body>
                                    <div class="message">
                                        <p class="text-center">
                                            <h6 style="text-align:center;">
                                                Next schedule to be published on: {{nextSchedule | date:'EEEE, MMMM d, yyyy'}}
                                            </h6>
                                        </p>
                                        The availabilty of recreational badminton at the <strong>University of Toronto Faculty of Kinesiology and Physical Education</strong>. This is the official schedule for badminton court bookings  publised weekly by the <strong>Faculty of Kinesiology and Physical Education</strong> and below listed is the schedule according to them.
                                        <br/><br/>
                                        <strong>Source:</strong><a href="http://physical.utoronto.ca/FitnessAndRecreation/Drop_In_Programs/Schedules.aspx"> University of Toronto Racquet Sport Bookings</a>
                                    </div>
                                </rd-widget-body>
                            </rd-widget>
                        </div>
                        <div class="col-lg-12">
                            <rd-widget>
                                <rd-widget-header title="Upper Gym Schedule">
                                </rd-widget-header>
                                <rd-widget-body>
                                    <div class="message" ng-if="data.schedule">
                                    <h3 style="text-align:center;" ng-show="calendarView == 'day'">Day of {{calendarDay | date:'EEEE, MMMM d, yyyy'}}</h3>
                                    <h3 style="text-align:center;" ng-show="calendarView == 'week'">Week of {{mondayOfWeek | date:'EEEE, MMMM d, yyyy'}} - {{sundayOfWeek | date:'EEEE, MMMM d, yyyy'}}</h3>
                                    <button ng-click="calendarView = 'week'" ng-show="calendarView == 'day'" class="btn btn-default">Back to Week View</button>
                                      <mwl-calendar
                                            view="calendarView"
                                            current-day="calendarDay"
                                            events="data.schedule"
                                            view-title="calendarTitle"
                                            on-event-click="eventClicked(calendarEvent)"
                                            on-event-times-changed="calendarEvent.startsAt = calendarNewEventStart; calendarEvent.endsAt = calendarNewEventEnd"
                                            edit-event-html="'<i class=\'glyphicon glyphicon-pencil\'></i>'"
                                            delete-event-html="'<i class=\'glyphicon glyphicon-remove\'></i>'"
                                            on-edit-event-click="eventEdited(calendarEvent)"
                                            on-delete-event-click="eventDeleted(calendarEvent)"
                                            auto-open="true"
                                            day-view-start="07:00"
                                            day-view-end="23:00">
                                        </mwl-calendar>
                                    </div>
                                </rd-widget-body>
                            </rd-widget>    
                        </div>  
                    </div>
                </div>

<!--                         <div class="col-lg-3 col-md-6 col-xs-12">
                            <rd-widget>
                                <rd-widget-body>
                                    <div class="title">{{data.TopBar.confirmed_bookings_today}}</div>
                                    <div class="comment">Confirmed Bookings Today</div>
                                </rd-widget-body>
                            </rd-widget>
                        </div>
                        <div class="col-lg-3 col-md-6 col-xs-12">
                            <rd-widget>
                                <rd-widget-body>
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
                                    <div class="title">{{data.TopBar.looking_to_play_this_week}}</div>
                                    <div class="comment">Looking To Play This Week</div>
                                </rd-widget-body>
                            </rd-widget>
                        </div> -->
                </div>

            </div><!-- End Page Content -->
        </div><!-- End Content Wrapper -->
    </div><!-- End Page Wrapper -->
</body>
</html>