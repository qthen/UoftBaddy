<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$user = User::get_current_user();
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
    <link rel="stylesheet" type="text/css" href="/css/tentative.css">
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

    <!--Scroll-->
    <script src="bower_components/angular-smoothscroll/dist/scripts/bb15da28.scripts.js"></script>

    <!--Affix -->
    <script src="/bower_components/ngScrollSpy/dist/ngScrollSpy.js"></script>
    <!-- Angular Moment -->
    <script src="bower_components/angular-moment/angular-moment.js"></script>
    <script type="text/javascript" src="/angular/tentative.js"></script>
</head>
<body ng-controller="controller" ng-init="init('<?php echo $user->user_id;?>')">
    <div id="page-wrapper" ng-class="{'open': toggle}" ng-cloak>
    <?php Renderer::get_sidebar();?>
        <div id="content-wrapper">
            <div class="page-content">

                <!-- Header Bar -->
<!--                 <div class="row header" style="margin:0px;padding:0px;">    
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
                            {{data.allThreads.length}} posts in total
                        </h3>
                    </div>
                    <div class="stats">
                        <h3 style="margin-right:5px;">
                            {{data.allThreads.length}} players planning to play this week
                        </h3>
                        <h3>
                            {{data.allThreadPlayers.length}} searching to play this week
                        </h3>
                    </div>
                </div>  

                <!-- Main Content -->
                <div ui-view>
                    <div class="col-lg-8">
                        <rd-widget>
                            <rd-widget-header icon="fa-users" title="Write Post">
                            </rd-widget-header>
                            <rd-widget-body>
                                <div class="message">
                                    <form class="form-horizontal" ng-show="data.showLookingToPlay">
                                        <div class="form-group">
                                            <div class="col-lg-12">
                                                <small style="float:right;">
                                                    Your post will be tagged with the <span class="label label-info">Looking To Play</span> tag
                                                </small>
                                            </div>
                                        </div>
                                        <div class="form-group">    
                                            <label class="col-lg-2 control-label">When</label>
                                            <div class="col-lg-10">
                                                <p class="input-group">
                                                    <input type="text" class="form-control" datepicker-popup="{{format}}" ng-model="data.dt" is-open="datepicker.opened" min-date="minDate" max-date="'2020-06-22'" datepicker-options="dateOptions" date-disabled="disabled(date, mode)" ng-required="true" close-text="Close" />
                                                    <span class="input-group-btn">
                                                        <button type="button" class="btn btn-default" ng-click="open($event)"><i class="glyphicon glyphicon-calendar"></i></button>
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
<!--                                         <div class="form-group">
                                            <label class="col-lg-2 control-label">Title</label>
                                            <div class="col-lg-10">
                                                <input type="text" ng-model="data.thread_title" class="form-control">
                                            </div>
                                        </div> -->
<!--                                         <div class="form-group">
                                            <label class="col-lg-2 control-label">Details</label>
                                            <div class="col-lg-10">
                                                <textarea ng-model="data.thread_text" class="form-control" rows="5">
                                                </textarea>
                                            </div>
                                        </div> -->
                                    </form>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <textarea placeholder="Looking to play some badminton, find a partner to regularly play with, or to ask about some badminton stuff?" rows="5" class="form-control" ng-model="data.thread_text" style="border:none;overflow: auto;outline: none;-webkit-box-shadow: none;-moz-box-shadow: none;box-shadow: none;" ng-click="data.showOptions = true">
                                            </textarea>
                                            <p class="help-text" ng-show="data.askQuestion">
                                                <small>
                                                    Are you looking to play badminton on some specific date? <a href ng-click="data.showLookingToPlay = true; data.asked = true;" style="margin-right:5px;">Yes</a><a href ng-click="data.showLookingToPlay = false; data.askQuestion = false; data.asked = true;">Unrelated</a>
                                                </small>
                                            </p>
                                        </div>
                                        <div class="col-lg-12" ng-show="data.showOptions">
                                            <p class="help-text" style="display:inline-block;">
                                                <small>
                                                    Will be posted as <a ng-href="profile.php?id={{user.user_id}}">
                                                        <img ng-src="{{user.avatar_link}}" style="height:25px;">{{user.username}}
                                                        </a>
                                                </small>
                                            </p>
                                            <button class="btn btn-primary" style="float:right;" ng-click="postThread()">Post</button>
                                        </div>
                                    </div>
                                </div>
                            </rd-widget-body>
                        </rd-widget>
                        <rd-widget ng-show="data.view == 1">
                            <rd-widget-header icon="fa-users" title="Threads ({{data.allThreads.length}})">
                                <small>
                                    <a href ng-click="data.view = 2">Only show posts with <span class="label label-info">Looking To Play</span>
                                    </a>
                                </small>
                            </rd-widget-header>
                            <rd-widget-body>
<!--                                 <div class="comment">
                                    <p class="text-center">
                                        <small>
                                            Displa
                                        </small>
                                    </p>    
                                </div> -->
                                <div ng-repeat="thread in data.allThreads" class="message">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="authorAvatar">
                                               <img ng-src="{{thread.author.avatar_link}}" class="img-responsive" style="width:auto;height:50px;">
                                                <span>
                                                    <a ng-href="profile.php?id={{thread.author.user_id}}">
                                                        {{thread.author.username}}
                                                    </a>
                                                    <br/>
                                                    <small ng-show="thread.type == 1" style="font-size:12px;color:#bdc3c7;">
                                                        {{thread.date_posted | date:'medium'}}&nbsp;&nbsp;<i class="fa fa-circle" style="font-size:3px;"></i>&nbsp;&nbsp;Looking to Play at {{thread.date_play | date:'MMM d, y'}}
                                                    </small>
                                                    <small ng-show="thread.type == 2" style="font-size:12px;color:#bdc3c7;">
                                                        Posted on {{thread.date_posted | date:'medium'}}
                                                    </small>
                                                </span>
                                            </div>
                                            <span style="float:right;" class="label label-info" ng-show="thread.type == 1">
                                                Looking to play
                                            </span>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="row">
                                                <div class="col-lg-12">
<!--                                                     <p>
                                                        <span class="label label-info">{{thread.str_type}}</span><i class="fa fa-chevron-right" style="margin-left:5px;margin-right:8px;"></i><a ng-href="thread.php?id={{thread.thread_id}}">{{thread.thread_title}}</a>
                                                    </p> -->
                                                    <p>
                                                        {{thread.thread_text}}
                                                    </p>
                                                    <hr>
                                                    <form>
                                                        <textarea ng-model="thread.possible_comment" class="form-control" placeholder="Your comment..." enter-submit="postThread()">
                                                        </textarea>
                                                    </form>
                                                </div>
                                                <div class="col-lg-3" ng-show="thread.showCommentBox">
                                                    <button class="btn btn-default" ng-click="postComment(thread.thread_id)">Post Comment</button>
                                                    <a href ng-click="thread.showCommentBox = false">Cancel</a>
                                                </div>
                                            </div>
                                        </div>
                                        <hr />
                                    </div>
                                </div>
                            </rd-widget-body>
                        </rd-widget>
                        <rd-widget ng-show="data.view == 2">
                            <rd-widget-header title="Threads({{data.allThreads.length}})">
                                <small>
                                    <a href ng-click="data.view = 1">Show all threads</a>
                                </small>
                            </rd-widget-header>
                            <rd-widget-body>
                                <div class="message">
                                    <div ng-repeat="threads in data.threads" class="message">
                                        <div id="{{$index}}">
                                        <h4 style="display:inline;">{{threads[0].date_play | date:'MMM d, y'}}</h4>
                                        <small style="display:inline;">({{threads.length}} looking to play)</small>
                                        <hr>
                                            <div ng-repeat="thread in threads">
                                                <div class="row">
                                                    <div class="col-lg-2">
                                                        <img ng-src="{{thread.author.avatar_link}}" class="img-responsive">
                                                    </div>
                                                    <div class="col-lg-10">
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <p>
                                                                    <span class="label label-info">{{thread.str_type}}</span><i class="fa fa-chevron-right" style="margin-left:5px;margin-right:8px;"></i><a ng-href="thread.php?id={{thread.thread_id}}">{{thread.thread_title}}</a>
                                                                </p>
                                                                <div>
                                                                    {{thread.thread_text}}
                                                                </div>
                                                                <div>
                                                                    <small>Tentative Court Date: {{thread.date_play | date:'MMM d, y'}}&nbsp;<i class="fa fa-circle"></i>&nbsp;Posted on {{thread.date_posted | date:'medium'}}&nbsp;<i class="fa fa-circle"></i>&nbsp;Views: 22</small>
                                                                    <a href ng-click="thread.showCommentBox = true">Add Comment</a>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-9" ng-show="thread.showCommentBox">
                                                                <textarea ng-model="thread.possible_comment" class="form-control" placeholder="Your comment...">
                                                                </textarea>
                                                            </div>
                                                            <div class="col-lg-3" ng-show="thread.showCommentBox">
                                                                <button class="btn btn-default" ng-click="postComment(thread.thread_id)">Post Comment</button>
                                                                <a href ng-click="thread.showCommentBox = false">Cancel</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </rd-widget-body>
                        </rd-widget>
                     </div>
                    <div class="col-lg-4" style="margin-bottom:20px;">
                        <rd-widget>
                            <rd-widget-header icon="fa-users" title="Thread Dates">
                            </rd-widget-header>
                            <rd-widget-body>
                                <div class="message" ng-repeat="thread in data.threads">
                                    <a smooth-scroll target="{{$index}}" ng-click="data.view = 2">{{thread[0].date_play | date:'MMM d, y'}}</a>
                                </div>
                                <div class="message" ng-show="data.threads.length == 0">
                                    No threads to show yet
                                </div>  
                            </rd-widget-body>
                        </rd-widget>
                    </div>
<!--                     <div class="col-lg-4" style="margin-bottom:20px;">
                        <rd-widget>
                            <rd-widget-header icon="fa-users" title="Statistics">
                            </rd-widget-header>
                            <rd-widget-body>
                                <div class="message">
                                </div>
                            </rd-widget-body>
                        </rd-widget>
                    </div> -->
                    <div class="col-lg-4">
                        <rd-widget>
                            <rd-widget-header icon="fa-users" title="Players ({{data.allThreadPlayers.length}} searching)">
                            </rd-widget-header>
                            <rd-widget-body>
                                <div class="message" ng-repeat="player in data.allThreadPlayers">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <a ng-href="profile.php?id={{player.user_id}}">
                                                <img ng-src="{{player.avatar}}" class="img-responsive">
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
                </div>  
            </div><!-- End Page Content -->
        </div><!-- End Content Wrapper -->
    </div><!-- End Page Wrapper -->
</body>
</html>