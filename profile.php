<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
if (isset($_GET['id']) && (is_numeric($_GET['id']))) {
    $profile_id = $_GET['id'];
    $user = User::get_current_user();
}
else {
    header('Location: 404.php');
}
$user = User::get_current_user();
if ($user instanceof AnonymousUser) {
    header('Location: /fblogin.php');
}
?>
<html lang="en" ng-app="app">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>UoftBaddy - {{profile.username}}</title>
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

    <!--AngularJS Sanitize-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.4.5/angular-sanitize.js">
    </script>

    <script src="bower_components/angular-moment/angular-moment.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>


    <!-- Custom Scripts -->
    <script type="text/javascript" src="/angular/profile.js"></script>
</head>
<body ng-controller="controller" ng-init="init('<?php echo $profile_id;?>', '<?php echo $user->user_id;?>')">
    <div id="page-wrapper" ng-class="{'open': toggle}" ng-cloak>

    <?php Renderer::get_sidebar();?>
        <div id="content-wrapper">
            <div class="page-content">

                <!-- Header Bar -->
                <!-- Header Bar -->
                <div class="row header">
                    <div class="col-xs-12">
                        <div class="user pull-right">
                            <div class="item dropdown">
                                <a href="#" class="dropdown-toggle">
                                    <img ng-src="{{profile.avatar_link}}"> 
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
                                Home / {{profile.username}}'s profile
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Header Bar -->

                <!-- Main Content -->
                <div ui-view>
                    <div class="row">
                        <div class="col-lg-8">
                            <div style="margin-bottom:10px;">
                                <rd-widget>
                                    <rd-widget-header title="Profile">
                                        <a href="editProfile.php" ng-if="user.user_id == profile.user_id">Edit Profile</a>
                                    </rd-widget-header>
                                    <rd-widget-body>
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <img class="img-responsive pull-left" ng-src="{{profile.avatar_link}}" style="height:150px;">
                                            </div>
                                            <div class="col-lg-8">
                                                <h3 style="padding-top:0px;margin-top:0px;">
                                                    {{profile.username}}
                                                </h3>
                                                <small>
                                                    {{profile.accolades}}
                                                </small>
                                                <h4>{{user.reputation}}</h4>
                                                <small>
                                                    Joined on {{profile.date_registered | date:'medium'}}
                                                </small>
                                                <br/>
                                                <small>
                                                     Last seen at {{profile.last_seen | date:'medium'}}
                                                </small>
                                            </div>
                                        </div>
                                    </rd-widget-body>
                                </rd-widget>
                            </div>
                            <div style="margin-bottom:10px;">
                                <rd-widget>
                                    <rd-widget-body>
                                        <div class="message">
                                            <p ng-show="profile.bio">
                                                {{profile.bio}}
                                            </p>
                                            <p ng-show="!profile.bio">
                                                This user has no uploaded summary
                                            </p>
                                        </div>
                                    </rd-widget-body>
                                </rd-widget>
                            </div>
                            <div>
                                <rd-widget>
                                    <rd-widget-header title="Timeline">
                                    </rd-widget-header>
                                    <rd-widget-body>
                                        <rd-loading ng-show="!profile.actions"></rd-loading>
                                        <div class="message" ng-repeat="action in profile.actions">
                                            <p ng-bind-html="action.message">
                                            </p>
                                            <small>
                                                {{action.action_message}}
                                            </smalll>
                                            <hr>
                                        </div>
                                        <div class="message" ng-show="profile.actions.length == 0">
                                            <p class="text-center">
                                                No activity yet
                                            </p>
                                        </div>
                                    </rd-widget-body>
                                </rd-widget>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div style="margin-bottom:10px;">
                                <rd-widget>
                                    <rd-widget-header title="About">
                                    </rd-widget-header>
                                    <rd-widget-body>
                                        <div class="message">
                                            <div style="margin-bottom:10px;">
                                                <h4 style="display:inline-block;margin-right:5px;margin-top:0px;">
                                                    Play Level:
                                                </h4>{{profile.literalPlaying}}
                                            </div>
                                            <div>
                                                <h4 style="display:inline-block;margin-right:5px;">
                                                    Program:
                                                </h4>
                                                {{profile.program}}
                                            </div>
                                        </div>
                                    </rd-widget-body>
                                </rd-widget>
                            </div>
                            <div style="margin-bottom:10px;">
                                <rd-widget>
                                    <rd-widget-header title="Statistics">
                                    </rd-widget-header>
                                    <rd-widget-body>
<!--                                         <div class="message">
                                            <div class="title">{{profile.reputation}} <h4 style="display:inline;">Reputation</h4><a href="upcoming.php">
                                                <i class="fa fa-question"></i>
                                            </a></div>
                                        </div> -->
                                        <div class="message">
                                            <p class="text-center">
                                                Joined around {{profile.number_of_joins}} confirmed court bookings
                                            </p>
                                        </div>
                                        <div class="message">
                                            <p class="text-center">
                                                Has notified absence about {{profile.number_of_leaves}} confirmed court bookings after committing to join
                                            </p>
                                        </div>
                                        <div class="message">
                                            <p class="text-center">
                                                Absent Ratio is {{profile.absence_ratio | limitTo: 3}} for every join
                                            </p>
                                        </div>
                                    </rd-widget-body>
                                </rd-widget>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- End Page Content -->
        </div><!-- End Content Wrapper -->
    </div><!-- End Page Wrapper -->
</body>
</html>