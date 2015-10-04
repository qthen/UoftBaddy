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
    <script type="text/javascript" src="/angular/courts.js"></script>
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
                                        <a href="notifications.php">
                                            See All
                                        </a>
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
                                Home / Booked Badminton Courts (Crowd Calendar)
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Header Bar -->
                <div class="col-lg-12 uoftBanner" style="margin-bottom:0px;padding:0px;">
                    <div class="uoftBannertitle">
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
                   <div class="col-lg-12" style="padding:0px;">
                    <nav class="navbar navbar-default">
                      <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false" class="navbar-toggle" ng-init="navCollapsed = true" ng-click="navCollapsed = !navCollapsed">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
 <!--      <a class="navbar-brand" href="#">UofTBaddy</a> -->
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1" ng-class="!navCollapsed && 'in'">
      <ul class="nav navbar-nav">
        <li ng-class="navbar.crowd"><a href="#" ng-click="handleNavbar('crowd')">Crowd Calendar<span class="sr-only">(current)</span></a></li>
        <li ng-class="navbar.info" ><a href="#" ng-click="handleNavbar('info')">Information</a></li>
        <li ng-class="navbar.stats"><a href="#" ng-click="handleNavbar('stats')">Stats</a></li>
<!--         <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>      
          <ul class="dropdown-menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="#">Separated link</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="#">One more separated link</a></li>
          </ul>
        </li> -->
      </ul>
<!--       <form class="navbar-form navbar-right" role="search" style="padding:0px;">
        <div class="form-group">
          <input type="text" class="form-control" placeholder="Search">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
      </form> -->
      <ul class="nav navbar-nav navbar-right">  
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">View By: {{dropdown.chosen}} <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="#" ng-click="handleDropdown('Today')">Today</a></li>
            <li><a href="#" ng-click="handleDropdown('Tomorrow')">Tomorrow</a></li>
            <li><a href ng-click="handleDropdown('Split')">Split</a></li>
          </ul>
        </li>
      </ul>
    </div>
    </div> 
         </nav>
            </div>
                <!-- Main Content -->
                <div ui-view>
                    <div class="row">
                        <div class="col-lg-12">
                            <div style="margin-bottom:10px;" ng-show="navbar.stats">
                                <rd-widget>
                                    <rd-widget-body>
                                        <div class="message">
                                            <p>
                                                Statistics on badminton dates, free courts, availabilty, usage, and many others is currently not available and will be made available soon. 
                                            </p>    
                                        </div>
                                    </rd-widget-body>
                                </rd-widget>
                            </div>
                            <div style="margin-bottom:10px;" ng-show="navbar.info">
                                <rd-widget>
                                    <rd-widget-body>
                                        <div class="title">
                                            Crowd Calendar
                                        </div>  
                                        <div class="message">
                                            <p class="text-center">
                                                <small>
                                                    <i>This list is as accurate as any contributors to this list make it to be. Please add your court bookings (whether with the intent to let others join or not) to inform other people of available times</i>    
                                                </small>
                                            </p>
                                        </div>
                                        <div class="message">   
                                            <p>
                                                The <strong>Crowd Calendar</strong> is a user-contributed calendar that display the booked badminton courts. This may be either for the purpose of finding other people to play with, or for informing users about when courts are free. The primary purpose of the crowd calendar is intended to be to find players to play with and is especially useful for <i>commuters</i> to plan their next day as their schedules tend to be a lot more inflexible than those who live near the University.
                                                <h3>Guidlines</h3>
                                                UoftBaddy is dedicated to creating a welcoming, friendly, and comfortable environment for badminton. Players come from all different types of background and diversity, and obviously not everyone's skill level will match others. Please include a brief description in your court posting (if you posted with intent to find other players) on exactly what type of badminton experience you are aiming for. A filled in profile will also help to showcase your exact skill level so that suitable players are easier to find. It is not discrimination to try to cater your court towards players of your skill level (or above/below), when you post your court, all users on the site have the choice of whether to join or not. This implies that there can be conflicts with joining badminton courts. As such conflicts are likely rare (unless for whatever reasons some user who has played badminton once or twice before joins a court with Varisty level players), you can try to handle them in the event conversation. We do <strong>not</strong> endorse forcing, offending, or rudely asking people to "get lost".  As such, please your discretion with this (however, this shouldn't be much of an issue, hopefully). However, there is a clear line between handling a situation (or just accepting it), and blantanly discriminating people due to other reasons. The same can also be said for users who "spam join" courts when much better alternatives are available as this points the person who booked the court and other particiapnts in an uncomfortable position. If you have <strong>any</strong> issues or feel like you are being unfairly treated, offended, threatened, etc. please contact us. 
                                                <br/><br/>
                                                On the topic of conversations, we encourage all users to be honest and treat each other with respect. Please speak all opinions and points politely, "Smashes are faster than clears, idiot" can be shortened to "Smashes are faster     than clears" and "You're stupid" can be shortened to nothing at all.   
                                                <h3>Accuracy and Absences</h3>
                                                We are also not responsible for the accuracy of the courts posted on the site, although several "spam" measures are in place to ensure this does not happen. A reputation system is already in <strong>beta</strong> and ready for deployment soon, if the site grows to a point such that this would be necessary, then it would be put out. If you think some user is spamming fake courts on the site, please contact us. 
                                                <br/><br/>  
                                                Furthermore, joining a court event means your are committing to the best of your knowledge that you will be available for the event. Joining and then not attending for no good reason (or if a notification of your absence could have been sent before) is disrespectful to the other participant(s), and we may be notified of it. This will also affect your future reputation and profile reputation. 
                                                <br/><br/>
                                                Finally, if you are looking for a regular partner or wish to play badminton in the future, you can go to <a href="tentative.php">Posts</a>
                                            </p>
                                            <p class="text-center">
                                                <strong>UoftBaddy regularly books courts everyday and offers it out on the site to help new members get courts easier. Normal court times are around 12 - 1
                                                </strong>
                                            </p>
                                        </div> 
                                    </rd-widget-body>
                                </rd-widget>
                            </div>
                            <div ng-show="navbar.crowd">
                                <rd-widget>
                                    <rd-widget-body>
                                        <div class="message">
                                            <div style="margin-bottom:5px;" ng-show="data.usersPlaying.length > 0">
                                                <img ng-repeat="user in data.usersPlaying" ng-src="{{user.avatar_link}}" style="height:50px;margin-right:5px;">
                                            </div>
                                            <div ng-show="data.usersPlaying.length == 0">
                                                <h3 style="display:inline-block;margin-bottom:2px;margin-top:0px;">- </h3><h4 style="display:inline;margin-bottom:2px;margin-top:0px;"> No users found</h4>
                                            </div>
                                            <small>
                                                {{data.usersPlaying.length}} <span ng-show="data.usersPlaying.length == 1">person</span><span ng-show="data.usersPlaying.length != 1">people</span> playing badminton today and tomorrow
                                            </small>
                                            <hr>
                                            <h3 style="display:inline-block;margin-bottom:2px;margin-top:0px;">{{data.todayFreeCourts.length}} </h3><h4 style="display:inline;margin-bottom:2px;margin-top:0px;"> court<span ng-show="data.todayFreeCourts.length != 1">s</span> with space left today</h4>
                                            <div ng-show="data.todayFreeCourts.length > 0">
                                                <small>
                                                    Free space during <span ng-repeat="court in data.todayFreeCourts">
                                                        {{court.begin_datetime | date:'h:00 a'}}<span ng-show="!$last">, </span> 
                                                    </span>
                                                </small>
                                            </div>
                                            <div ng-show="data.todayFreeCourts.length == 0">
                                                <small>
                                                    No free space left according to the crowd calendar submissions  
                                            </div>
                                            <span ng-repeat="badmintonDate in data.todayFreeCourts"></span>
                                            <hr>
                                            <h3 style="display:inline-block;margin-top:0px;margin-bottom:2px;">{{data.tomorrowFreeCourts.length}} </h3><h4 style="display:inline;margin-top:0px;margin-bottom:2px;"> court<span ng-show="data.tomorrowFreeCourts.length != 1">s</span> with space left tomorrow</h4>
                                            <br/>
                                            <div ng-show="data.tomorrowFreeCourts.length > 0">
                                                <small>
                                                    Free space during <span ng-repeat="court in data.tomorrowFreeCourts">
                                                        {{court.begin_datetime | date:'h:00 a'}}<span ng-show="!$last">, </span> 
                                                    </span>
                                                </small>
                                            </div>
                                            <div ng-show="data.tomorrowFreeCourts.length == 0">
                                                <small>
                                                    No free space left according to the crowd calendar submissions  
                                                </small>
                                            </div>
                                            <span ng-repeat="badmintonDate in data.tomorrowFreeCourts"></span>
                                        </div>
                                    </rd-widget-body></small>
                                </rd-widget>
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
                    <div class="row" ng-show="navbar.crowd">
                        <div class="col-lg-12" ng-show="dropdown.chosen == 'Tomorrow'">
                            <rd-widget>
                                <rd-widget-header icon="fa-users" title="Tomorrow - {{tomorrowDay | date:'EEEE, MMMM d, yyyy'}}">
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
                        <div class="col-lg-12" ng-show="dropdown.chosen == 'Today'">
                            <rd-widget>
                                <rd-widget-header icon="fa-users" title="Today - {{calendarDay | date:'EEEE, MMMM d, yyyy'}}">
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
                        <div class="col-lg-6" ng-show="dropdown.chosen == 'Split'">
                            <rd-widget>
                                <rd-widget-header icon="fa-users" title="Today - {{calendarDay | date:'EEEE, MMMM d, yyyy'}}">
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
                        <div class="col-lg-6" ng-show="dropdown.chosen == 'Split'">
                            <rd-widget>
                                <rd-widget-header icon="fa-users" title="Tomorrow - {{tomorrowDay | date:'EEEE, MMMM d, yyyy'}}">
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
   <!--                      <div class="col-lg-4">
                              <h4>Inline</h4>
    <div style="display:inline-block; min-height:290px;">
      <datepicker ng-model="dt" min-date="minDate" show-weeks="true" class="well well-sm" custom-class="getDayClass(date, mode)"></datepicker>
    </div>
    </div> -->
                    </div>
                </div>

            </div><!-- End Page Content -->
        </div><!-- End Content Wrapper -->
    </div><!-- End Page Wrapper -->
</body>
</html>