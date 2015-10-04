<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $thread_id = $_GET['id'];
}
else {
    header('Location: 404.php');
}
?>
<html lang="en" ng-app="app">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Looking To Play</title>
    <!-- STYLES -->
    <!-- build:css lib/css/main.min.css -->
    <link rel="stylesheet" type="text/css" href="/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="/bower_components/rdash-ui/dist/css/rdash.min.css">
    <link rel="stylesheet" type="text/css" href="/css/thread.css">
    <!-- endbuild -->
    <!-- SCRIPTS -->
    <!-- build:js lib/js/main.min.js -->
    <script type="text/javascript" src="/bower_components/angular/angular.min.js"></script>
    <script type="text/javascript" src="/bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js"></script>
    <script type="text/javascript" src="/bower_components/angular-cookies/angular-cookies.min.js"></script>
    <script type="text/javascript" src="/bower_components/angular-ui-router/release/angular-ui-router.min.js"></script>
    <!-- endbuild -->
    <link rel="stylesheet" href="bower_components/fullcalendar/dist/fullcalendar.css"/>
    <!-- Custom Fonts -->
    <link href="bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- jQuery -->
    <script src="/bower_components/jquery/dist/jquery.min.js"></script>
    <!--Calendar-->
    <script type="text/javascript" src="bower_components/moment/min/moment.min.js"></script>
    <script type="text/javascript" src="bower_components/angular-ui-calendar/src/calendar.js"></script>
    <script type="text/javascript" src="bower_components/fullcalendar/dist/fullcalendar.min.js"></script>
    <script type="text/javascript" src="bower_components/fullcalendar/dist/gcal.js"></script>
    <script src="bower_components/ngDialog/js/ngDialog.js"></script>
    <!-- Custom Scripts -->

    <!-- Angular Moment -->
    <script src="bower_components/angular-moment/angular-moment.js"></script>
    <script type="text/javascript" src="/angular/thread.js"></script>
</head>
<body ng-controller="controller" ng-init="init('<?php echo $thread_id;?>')">
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
                                Home / Thread
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Header Bar -->

                <!-- Main Content -->
                <div ui-view>
                    <div class="row">
                        <div class="col-lg-8">
                            <rd-widget>
                                <rd-widget-body style="padding-top:0px;margin-top:0px;">
                                    <div class="authorAvatar" style="margin-bottom:10px;">  
                                       <img ng-src="{{thread.author.avatar_link}}" class="img-responsive" style="width:auto;height:50px;">
                                        <span>
                                            <a ng-href="profile.php?id={{thread.author.user_id}}">
                                                {{thread.author.username}}
                                            </a>
                                            <br/>
                                            <small ng-show="thread.type == 1" style="font-size:12px;color:#bdc3c7;">
                                                <span am-time-ago="thread.date_posted"></span>&nbsp;&nbsp;<i class="fa fa-circle" style="font-size:3px;margin-top:2px;"></i>&nbsp;&nbsp;<span class="label label-info">Looking to Play</span>    at {{thread.date_play | date:'MMM d, y'}}
                                            </small>
                                            <small ng-show="thread.type == 2" style="font-size:12px;color:#bdc3c7;">
                                                <span am-time-ago="thread.date_posted"></span>
                                            </small>
                                        </span>
                                    </div>
<!--                                     <div class="message" style="margin-bottom:15px;font-size:12px;color:#bdc3c7;">
                                        <small>
                                            Posted {{thread.date_posted | date:'medium'}}
                                        </small>
                                    </div>   -->
                                    <div class="message">
                                        <p>
                                            {{thread.thread_text}}
                                        </p>
                                    </div>
                                    <hr>
                                    <div ng-repeat="comment in thread.comments" style="margin-left:8%;">
                                        <div class="authorAvatar">
                                           <img ng-src="{{comment.author.avatar_link}}" class="img-responsive" style="width:auto;height:40px;">
                                            <span>
                                                <a ng-href="profile.php?id={{comment.author.user_id}}">
                                                    {{comment.author.username}}
                                                </a>
                                                <br/>
                                                <small style="font-size:12px;color:#bdc3c7;">
                                                    <span am-time-ago="comment.date_posted"></span>
                                                </small>
                                            </span>
                                        </div>
                                        <a href ng-click="delete(comment)">
                                            <span class="glyphicon glyphicon-remove" style="float:right;"></span>
                                        </a>
                                        <p>
                                            {{comment.comment_text}}
                                        </p>
                                        <hr style="opacity:0.7;margin:0px;">
                                    </div>
                                    <div ng-repeat="comment in data.thread.comments">
                                        {{comment.comment_text}}
                                    </div>
                                    <textarea ng-model="data.possibleComment" class="form-control" rows="3" placeholder="Your comment...." enter-submit="postComment()">
                                    </textarea>
                                </rd-widget-body>
                            </rd-widget>
                        </div>
<!--                         <div class="col-lg-4" style="margin-bottom:10px;">
                            <rd-widget>
                                <rd-widget-header icon="fa-users" title="In Thread ({{threadParticipants.length}} users)">
                                </rd-widget-header>
                                <rd-widget-body>
                                    <div class="message" ng-repeat="player in threadParticipants">
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <a ng-href="profile.php?id={{player.user_id}}">
                                                    <img ng-src="{{player.avatar_link}}" class="img-responsive">
                                                </a>
                                            </div>
                                            <div class="col-lg-8">
                                                <a ng-href="profile.php?id={{player.user_id}}">
                                                    <h5>{{player.username}}</h5>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </rd-widget-body>
                            </rd-widget>
                        </div>
                        <div class="col-lg-4">
                            <rd-widget>
                                <rd-widget-header title="Recent Posts">
                                </rd-widget-header>
                                <rd-widget-body>
                                </rd-widget-body>
                            </rd-widget>
                        </div> -->
                    </div>
                </div>

            </div><!-- End Page Content -->
        </div><!-- End Content Wrapper -->
    </div><!-- End Page Wrapper -->
</body>
</html>