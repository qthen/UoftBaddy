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
    <link rel="stylesheet" type="text/css" href="bower_components/ngDialog/css/ngDialog.css">
    <link rel="stylesheet" type="text/css" href="/css/courts.css">
    <link rel="stylesheet" type="text/css" href="bower_components/ngDialog/css/ngDialog-theme-default.css">
    <!-- endbuild -->
    <!-- SCRIPTS -->
    <!-- build:js lib/js/main.min.js -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.4.5/angular.min.js"></script>
    <script src="bower_components/moment/min/moment.min.js"></script>
    <link href="bower_components/angular-bootstrap-calendar/dist/css/angular-bootstrap-calendar.min.css" rel="stylesheet">
    <script src="bower_components/angular-bootstrap-calendar/dist/js/angular-bootstrap-calendar-tpls.min.js"></script>
    <script type="text/javascript" src="/bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js"></script>
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
    <script type="text/javascript" src="/angular/courts.js"></script>
</head>
<body ng-controller="controller" ng-init="init('<?php echo $user->user_id;?>')">
    <div id="page-wrapper" ng-class="{'open': toggle}" ng-cloak>

    <?php Renderer::get_sidebar();?>
        <div id="content-wrapper">
            <div class="page-content">

                <!-- Header Bar -->
<!--                 <div class="row header">
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
                                        <a href="#">Server Down!</a>
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
                </div> -->
                <!-- End Header Bar -->
                <div class="col-lg-12 uoftBanner" style="margin-bottom:10px;padding:0px;">
                    <div class="title">
                        <h1>
                            UoftBaddy - Courts
                        </h1>
                        <hr style="margin-top:3px;margin-bottom:5px;padding:0px;">
                        <h3 style="margin-top:0px;padding:0px;">
                            {{todayEvents.length + tomorrowEvents.length}} Confirmed Badminton Courts
                        </h3>   
                    </div>
                    <div class="stats">
                        <h3>{{todayEvents.length}} courts today</h3>
                        <h3>{{tomorrowEvents.length}} courts tomorrow</h3>
                    </div>
                </div> 
                <!-- Main Content -->
                <div ui-view>
                    <div class="row">
                        <div class="col-lg-12">
                            <rd-widget>
<!--                                 <rd-widget-header title="Confirmed Courts">
                                </rd-widget-header> -->
                                <rd-widget-body>
                                    <div class="message">
                                        <p class="text-center">
                                            <small>
    <!--                                             Displaying most likely estimate of the <strong>University of Toronto</strong> badminton booking schedule
                                                <br/> -->
                                                <i>This list is as accurate as any contributors to this list make it to be. Please add your court bookings (whether with the intent to let others join or not) to inform other people of available times</i>    
                                            </small>
                                        </p>
                                    </div>
                                    <div class="message">   
                                        <p>
                                            All courts listed here are <strong>confirmed</strong> badminton courts that have been posted by their respective bookers and is intended for an informative list and a way to find players if you have court! If you are looking for a regular partner or wish to play badminton in the future, you can go to <a href="tentative.php">Posts</a>
                                        </p>
<!--                                         <p>
                                            All user-submitted confirmed booked badminton courts (whether with the intent to find other players or for informative purposes) are posted here, as booking courts at the <strong>Faculty of Kinesiology and Physical Education</strong> (the primary location for recreational badminton at the <strong>University of Toronto</strong>) can only be booked the day of or the day before, all court bookings are under the view limitation of today or tomorrow. If you are looking for a partner to play with or wish to play badminton in the future you can go to <a href="tentative.php">Looking To Play</a>
                                        </p> -->
                                        <p class="text-center">
                                            <strong>UoftBaddy regularly books courts everyday and offers it out on the site to help new members get courts easier. Normal court times are around 12 - 1
                                            </strong>
                                        </p>
                                    </div> 
                                </rd-widget-body>
                            </rd-widget>
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
                    <div class="row">
                        <div class="col-lg-6">
                            <rd-widget>
                                <rd-widget-header icon="fa-users" title="Today">
                                    <a href="#" ng-click="createDateDialog('today')">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </rd-widget-header>
                                <rd-widget-body>
                                    <div class="message" ng-if="todayEvents">
                                      <mwl-calendar
                                            view="calendarView"
                                            current-day="calendarDay"
                                            events="todayEvents"
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
                        <div class="col-lg-6">
                            <rd-widget>
                                <rd-widget-header icon="fa-users" title="Tomorrow">
                                    <a href="#" ng-click="createDateDialog('tomorrow')">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </rd-widget-header>
                                <rd-widget-body>
                                    <div class="message" ng-if="tomorrowEvents">
                                      <mwl-calendar
                                            view="calendarView"
                                            current-day="tomorrowDay"
                                            events="tomorrowEvents"
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