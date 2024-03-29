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
    <script type="text/javascript" src="/angular/index/index.js"></script>
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
                                Discussion
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
                            "All courts are booked"
                            <br/>
                            Finally, social badminton at UofT done right
                        </h3>
                    </div>
                    <div class="stats">
                        <h3 style="margin-right:5px;">
                            247 total users
                        </h3>
                        <h4>
                            26 signed up today
                        </h4>
                    </div>
                    <div class="credits">
                        <small>Written in: PHP 5.6 (server-side), MySQL 5.5 (database), Javascript, and AngularJS 1.4 Web Framework</small>
                    </div>
                </div>  

                <!-- Main Content -->
                <div ui-view>
                    <div class="col-lg-12">
                        <rd-widget>
                            <rd-widget-header title="About">
                            </rd-widget-header>
                            <rd-widget-body>
                                <div class="message">
                                    <a href="news.php?id=1">
                                        <h3>UoftBaddy</h3>
                                    </a>
                                    <p class="text-center">
                                        <small>This is an experiment to see how many people need this sort of serivce. Any feedback helps</small>
                                    </p>
                                    <p>
                                        As somebody who avidly plays badminton, I was a bit disappointed at the way badminton courts are allocated at UofT. Firstly, almost all courts are booked by the time the clock reaches 8. For many commuters and students who don't know many friends who enjoy playing badminton, this is quite difficult to get involved (especially since athletic fees are a mandatory part of our tuitition at the school, I believe we have the right to use it to our advantage).
                                        <br/><br/>
                                        Commuters encounter a schedule problem since booking courts must be done within a pretty small time-limit (24 hours in advance) and for the most part, their schedule is unchangeable during the day and plans must be made the day before. It's hard to plan as a commuter especially since our schedules aren't as flexibly as those living close by to the univeristy. Considering the fact that University of Toronto is a commuter school (almost 40% of students commute every day in some form), and the competition to get a single badminton court at University of Toronot (which only normally houses 3 courts for a massive school population), and lack of time to find new partners to play with, playing badminton even irregularly becomes quite diffcult, and a dependable regularity becomes almost an impossibility.
                                        <br/><br/>
                                        For those living near the school, there are no services to find partners to play with unlike <a href="#">SquashOrbit</a> for squash. The facebook group for UofT Badminton is largely inefficient (not to mention that commuters can practically ignore any request to play the day of). 
                                    </p>
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