<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $date_id = $_GET['id'];
}
else {
  header('Location:: http://uoftbaddy.ca/404.php');
}
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


            <!-- Angular Moment -->
        <script src="bower_components/angular-moment/angular-moment.js"></script>



        <!-- Bootstrap Core JavaScript -->
        <script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>


    <!-- Custom Scripts -->
    <script type="text/javascript" src="/angular/date.js"></script>
</head>
<body ng-controller="controller" ng-init="init('<?php echo $date_id;?>')">
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
                                    <img src="img/avatar.jpg">
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li class="dropdown-header">
                                        Joe Bloggs
                                    </li>
                                    <li class="divider"></li>
                                    <li class="link">
                                        <a href="#">
                                            Profile
                                        </a>
                                    </li>
                                    <li class="link">
                                        <a href="#">
                                            Menu Item
                                        </a>
                                    </li>
                                    <li class="link">
                                        <a href="#">
                                            Menu Item
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                    <li class="link">
                                        <a href="#">
                                            Logout
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="item dropdown">
                             <a href="#" class="dropdown-toggle">
                                    <i class="fa fa-bell-o"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li class="dropdown-header">
                                        Notifications
                                    <li class="divider"></li>
                                    <li>
                                        <a href="#">Server Down!</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="meta">
                            <div class="page">
                                Dashboard
                            </div>
                            <div class="breadcrumb-links">
                                Home / Dashboard
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Header Bar -->

                <!-- Main Content -->
                <div ui-view>
                    <div class="col-lg-8">
                    </div>
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-lg-12">
                                <rd-widget>
                                    <rd-widget-header title="{{data.badmintonDate.datename}}">
                                    </rd-widget-header>
                                    <rd-widget-body>
                                        <div class="message">
                                            <small>
                                                {{data.badmintonDate.message}}
                                            </small>
                                        </div>
                                        <div class="comment">
                                            {{data.badmintonDate.summary}}
                                        </div>
                                    </rd-widget-body>
                                </rd-widget>
                            </div>
                            <div class="col-lg-12">
                                <rd-widget>
                                    <rd-widget-header title="This Event's Conversation">
                                    </rd-widget-header>
                                    <rd-widget-body>
                                        <div class="message" ng-repeat="message in data.badmintonDate.conversation.messages">
                                        </div>
                                    </rd-widget-body>
                                </rd-widget>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <rd-widget>
                            <rd-widget-header title="About">
                            </rd-widget-header>
                            <rd-widget-body>
                                <div class="message">
                                    <p class="text-center">
                                        <strong>Creator:</strong> <a ng-href="profile.php?id={{data.badmintonDate.creator.user_id}}">{{data.badmintonDate.creator.email}}</a>
                                    </p>
                                    <p class="text-center">
                                        <strong>Location:</strong> Upper Gym
                                    </p>
                                    <p class="text-center">
                                        <strong>Joined Participants:</strong> {{data.badmintonDate.number_of_attendants}}
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