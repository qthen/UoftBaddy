<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$user = User::get_current_user();
$tabs = array(
    'upcoming'
);
if (isset($_GET['tab'])) {
    $_GET['tab'] = strtolower($_GET['tab']);
    if (in_array($_GET['tab'], $tabs)) {
        $tab = $_GET['tab'];
    }
    else {
        $tab = 'all';
    }
}
else {
    $tab = 'all';
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

    <!--Scroll-->
    <script src="bower_components/angular-smoothscroll/dist/scripts/bb15da28.scripts.js"></script>

    <!--Affix -->
    <script src="/bower_components/ngScrollSpy/dist/ngScrollSpy.js"></script>
    <!-- Angular Moment -->
    <script src="bower_components/angular-moment/angular-moment.js"></script>
    <script type="text/javascript" src="/angular/all.js"></script>
</head>
<body ng-controller="controller" ng-init="init('<?php echo $user->user_id;?>', '<?php echo $tab;?>')">
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
                    <div class="col-lg-12">
                        <rd-widget>
                            <rd-widget-header icon="fa-users" title="Your Events">
                            </rd-widget-header>
                            <rd-widget-body>
                                <div class="message">
                                    <p>
                                        All your events that you have particpated will be shown here.
                                        <br/>
                                        <small>Currently showing {{data.filteredEvents.length}} events out of {{outOf}} of all your events</small>
                                    </p>
                                </div>
                                <tabset justified="true">
                                    <tab heading="All" active="tab.all" select="updateView('all')">
                                        <div ng-repeat="event in data.filteredEvents">
                                            <h4><a ng-href="date.php?id={{event.date_id}}">{{event.datename}}</a></h4>
                                            <small>
                                                {{event.message}}
                                            </small>
                                            <hr>
                                        </div>
                                        <div ng-show="data.filteredEvents.length == 0" style="margin-top:30px;">
                                            <p class="text-center">
                                                You have not participated in any events yet.. what are you waiting for?
                                            </p>
                                        </div>
                                    </tab>
                                    <tab heading="Upcoming" active="tab.upcoming" select="updateView('upcoming')">
                                        <div ng-repeat="event in data.filteredEvents" select="updateView('upcoming')">
                                            <h4><a ng-href="date.php?id={{event.date_id}}">{{event.datename}}</a></h4>
                                            <small>
                                                {{event.message}}
                                            </small>
                                            <hr>
                                        </div>
                                    </tab>
                                    <tab heading="Past" active="tab.past" select="updateView('past')">
                                        <div ng-repeat="event in data.filteredEvents">
                                            <h4><a ng-href="date.php?id={{event.date_id}}">{{event.datename}}</a></h4>
                                            <small>
                                                {{event.message}}
                                            </small>
                                            <hr>
                                        </div>
                                    </tab>
                                    <tab heading="Hosted By You" active="tab.hosted" select="updateView('you')">
                                        <div ng-repeat="event in data.filteredEvents">
                                            <h4><a ng-href="date.php?id={{event.date_id}}">{{event.datename}}</a></h4>
                                            <small>
                                                {{event.message}}
                                            </small>
                                            <hr>
                                        </div>
                                    </tab>
                                </tabset>
                            </rd-widget-body>
                        </rd-widget>
                    </div>
                </div>

            </div><!-- End Page Content -->
        </div><!-- End Content Wrapper -->
    </div><!-- End Page Wrapper -->
</body>
</html>