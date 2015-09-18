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
<body ng-controller="controller" ng-init="init('<?php echo $profile_id;?>')">
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
                                    <img ng-src="{{profile.avatar}}">
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
                    <div class="row">
                        <div class="col-lg-12">
                            <rd-widget>
                                <rd-widget-header title="Edit Profile">
                                </rd-widget-header>
                                <rd-widget-body>
                                    <form>
                                        <fieldset>
                                            <div class="form-group">
                                                <label class="control-label col-lg-2">Username</label>
                                                <div class="col-lg-10">
                                                    <p class="form-control-static">
                                                        {{user.username}}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-lg-2">Connected With:</label>
                                                <div class="col-lg-10">
                                                    <p class="form-control-static">
                                                        Facebook
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-lg-2">Program of Study
                                                </label>
                                                <div class="col-lg-10">
                                                    <input type="text" class="form-control" ng-model="user.program">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-lg-2">Commuter</label>
                                                <div class="col-lg-10">
                                                    <label class="radio-inline">
                                                    <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="1" ng-model="data.commuter">Yes
                                                    </label>
                                                    <label class="radio-inline">
                                                    <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="0" ng-model="data.commuter">No
                                                    </label>
                                                    <label class="radio-inline">
                                                    <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="2" ng-model="data.commuter">Unspecified
                                                    </label>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </form>
                                </rd-widget-body>
                            </rd-widget>
                        </div>
                    </div>
                </div>
            </div><!-- End Page Content -->
        </div><!-- End Content Wrapper -->
    </div><!-- End Page Wrapper -->
</body>
</html>