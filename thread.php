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
                </div>
                <!-- End Header Bar -->

                <!-- Main Content -->
                <div ui-view>
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-lg-12" style="margin-bottom:20px;">
                                <rd-widget>
                                    <rd-widget-header title="{{thread.author.username}}" style="margin-bottom:2px;padding-bottom:0px;">
                                    </rd-widget-header>
                                    <rd-widget-body style="padding-top:0px;margin-top:0px;">
                                        <div class="title">
                                            {{thread.thread_title}}
                                        </div> 
                                        <div class="comment" style="margin-bottom:15px;font-size:12px;color:#bdc3c7;">
                                            <small>
                                                Posted {{thread.date_posted | date:'medium'}}
                                            </small>
                                        </div>  
                                        <div class="message">
                                            <p>
                                                {{thread.thread_text}}
                                            </p>
                                        </div>
                                        <hr>
                                        <textarea ng-model="data.possibleComment" class="form-control" rows="3" placeholder="Your comment...." enter-submit="postComment()">
                                        </textarea>
                                    </rd-widget-body>
                                </rd-widget>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
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
                        <rd-widget>
                            <rd-widget-header title="Recent Posts">
                            </rd-widget-header>
                            <rd-widget-body>
                            </rd-widget-body>
                        </rd-widget>
                    </div>
                </div>

            </div><!-- End Page Content -->
        </div><!-- End Content Wrapper -->
    </div><!-- End Page Wrapper -->
</body>
</html>