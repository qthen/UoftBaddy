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

    <title>The UofTBaddy Project</title>
    <!-- STYLES -->
    <!-- build:css lib/css/main.min.css -->
    <link rel="stylesheet" type="text/css" href="/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="/bower_components/rdash-ui/dist/css/rdash.min.css">
    <link rel="stylesheet" type="text/css" href="/css/index.css">
    <!-- endbuild -->
    <!-- SCRIPTS -->
    <!-- build:js lib/js/main.min.js -->
    <script type="text/javascript" src="/bower_components/angular/angular.min.js"></script>
    <script type="text/javascript" src="/bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js"></script>
    <script type="text/javascript" src="/bower_components/angular-cookies/angular-cookies.min.js"></script>
    <script type="text/javascript" src="/bower_components/angular-ui-router/release/angular-ui-router.min.js"></script>
    <link rel="stylesheet" type="text/css" href="bower_components/ngDialog/css/ngDialog.css">
    <link rel="stylesheet" type="text/css" href="bower_components/ngDialog/css/ngDialog-theme-default.css">
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
    <script type="text/javascript" src="/angular/feedback.js"></script>
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
                                    <i class="fa fa-bell-o"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li class="dropdown-header">
                                        Notifications
                                        <span class="badge">{{data.notifications.length}}</span>
                                    </li>
                                    <li class="divider"></li>
                                    <li ng-repeat="notification in data.notifications">
                                        <a ng-href="{{notification.a_href}}">
                                            {{notification.message}}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="meta" style="margin:0px;padding:0px;">   
                            <div class="page">
                                UoftBaddy
                            </div>
                            <div class="breadcrumb-links">
                                Home / Contact Us
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Header Bar -->
                <div class="col-lg-12 uoftBanner" style="margin-bottom:10px;padding:0px;">
                    <div class="uoftTitle">
                        <h1>
                            UoftBaddy
                        </h1>
                        <hr style="margin-top:3px;margin-bottom:5px;padding:0px;">
                        <h3 style="margin-top:0px;padding:0px;">
                            "UoftBaddy here, how can we help?"
                            <br/>
                        </h3>
                    </div>
<!--                     <div class="stats">
                        <h3 style="margin-right:5px;">
                            247 total users
                        </h3>
                        <h4>
                            26 signed up today
                        </h4>
                    </div> -->
                    <div class="credits">
                        <small>Written in: PHP 5.6 (server-side), MySQL 5.5 (database), Javascript, and AngularJS 1.4 Web Framework</small>
                    </div>
                </div>  

                <!-- Main Content -->
                <div ui-view>
                    <div class="col-lg-12" style="margin-bottom:10px;">
                        <rd-widget>
                            <rd-widget-body>
                                <div class="message">
                                    We're not perfect (or maybe the more fitting subject is I'm not since the site is developed by a sole developer). However, the site is not me, but all the users who use this. If the purpose of the site is to be a community-driven, social site for badminton at <strong>University of Toronto</strong>, then it should go without saying that your feedback is very valuable. If you have something (anything!) to say, ask, criticize, contribute, join, talk, etc., please submit it below, I read all of them!
                                    <br/><br/>
                                    If you wish for me to contact you back (or to know who you are for whatever reason), please make sure "Submit anonymously" is unchecked. Your anonyomity is guaranteed on the site (it is never sent in any form to the server, the only thing possibly retreivable is your IP Address throgh server logs, but would take a long time to figure out). As <strong>UoftBaddy</strong> is open source, you can check the code on Github if you have any lingering suspicions. 
                                </div>
                            </rd-widget-body>
                        </rd-widget>
                    </div>
                    <div class="col-lg-12">
                        <rd-widget>
                            <rd-widget-body>
                                <div class="message">
                                    <form class="form-horizontal">
                                        <fieldset>
                                            <div class="form-group">
                                                <label class="col-lg-2 control-label">Username</label>
                                                <div class="col-lg-10">
                                                    <p class="form-control-static" ng-show="data.anonymous == 2">
                                                        {{user.username}}
                                                    </p>
                                                    <p class="form-control-static" ng-show="data.anonymous == 1">
                                                        Anonymous
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-2 control-label">Your Message</label>
                                                <div class="col-lg-10">
                                                    <textarea ng-model="data.message" class="form-control" rows="5" required></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-2 control-label">Anonymity</label>
                                                <div class="col-lg-10">
                                                    <div class="radio-inline">
                                                        <label>
                                                        <input type="radio" name="optionsRadios" id="optionsRadios1" value="2" ng-model="data.anonymous">
                                                        Remain un-anonymous
                                                        </label>
                                                    </div>
                                                    <div class="radio-inline">
                                                        <label>
                                                        <input type="radio" name="optionsRadios" id="optionsRadios2" value="1" ng-model="data.anonymous">
                                                        Remain anonymous
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <input type="submit" class="btn btn-primary" value="Submit" ng-click="submitFeedback()">
                                            </div>
                                        </fieldset>
                                    </form>
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