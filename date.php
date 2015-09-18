<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
$user = User::get_current_user();
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
    <link rel="stylesheet" type="text/css" href="/css/date.css">
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
<body ng-controller="controller" ng-init="init('<?php echo $date_id;?>', '<?php echo $user->user_id;?>')">
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
                            <div class="col-lg-12">
                                <rd-widget>
                                    <rd-widget-header title="Badminton">
                                    </rd-widget-header>
                                    <rd-widget-body>
                                        <div class="title">
                                            {{data.badmintonDate.datename}}
                                        </div>
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
                                        <div class="row" ng-show="((user.user_id == data.badmintonDate.creator.user_id) && (data.badmintonDate.begin_datetime < Date()))">
                                            <div class="col-lg-4 col-lg-offset-8">
                                                <button class="btn btn-danger" ng-click="promptConfirmation()">Close Conversation
                                                </button>
                                            </div>
                                        </div>
                                        <div class="message" ng-repeat="message in data.badmintonDate.conversation.messages">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="row">
                                                        <div class="col-lg-3">
                                                            <img ng-src="{{message.author.avatar}}" class="img-responsive">
                                                        </div>
                                                        <div class="col-lg-9">
                                                            <h5><a ng-href="profile.php?id={{message.author.user_id}}">{{message.author.username}}</a></h5>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <hr>
                                                </div>
                                                <div class="col-lg-12">
                                                    <p>
                                                        {{message.message_text}}
                                                    </p>
                                                </div>
                                                <div class="col-lg-12">
                                                    <small>
                                                        <a ng-href="postReply(message)">
                                                            Reply
                                                        </a>
                                                        {{message.moment_posted}}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class"message" ng-show="data.badmintonDate.conversation.messages.length == 0 && !data.badmintonDate.conversation.closed">
                                            <p class="text-center">
                                                This event's conversation currently has no messages yet, be the first one below?
                                            </p>
                                        </div>
                                        <div class="message" ng-show="data.badmintonDate.conversation.closed">
                                            <p class="text-center">
                                                <h6>This event's conversation has been closed by the creator.</h6>
                                            </p>
                                        </div>
                                        <div class="postMessage" ng-show="!data.badmintonDate.conversation.closed">
                                            <div class="row">
                                                <div class="col-lg-10">
                                                    <textarea ng-model="data.possibleMessage" class="form-control" rows="4">
                                                    </textarea>
                                                </div>
                                                <div class="col-lg-2">
                                                    <button class="btn btn-default" ng-click="postMessage()">Post</button>
                                                </div>
                                            </div>
                                        </div>
                                    </rd-widget-body>
                                </rd-widget>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <rd-widget>
                            <rd-widget-header title="Details">
                            </rd-widget-header>
                            <rd-widget-body>
                                <div class="message">
                                    <p class="text-center">
                                        <strong>Creator:</strong> <a ng-href="profile.php?id={{data.badmintonDate.creator.user_id}}">{{data.badmintonDate.creator.username}}</a>
                                    </p>
                                    <p class="text-center">
                                        <strong>Optimal Number of Participants:</strong> {{data.badmintonDate.max_attendants}}
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