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
    <link rel="stylesheet" type="text/css" href="bower_components/ngDialog/css/ngDialog.css">
    <link rel="stylesheet" type="text/css" href="bower_components/ngDialog/css/ngDialog-theme-default.css">
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
                                Home / Badminton Court
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
                                <div style="margin-bottom:10px;">
                                    <rd-widget>
                                        <rd-widget-header title="Badminton">
                                        </rd-widget-header>
                                        <rd-widget-body>
                                            <div class="title"> 
                                                {{data.badmintonDate.datename}}
                                            </div>
                                            <hr style="margin-top:5px;margin-bottom:5px;">
                                            <div>
                                                <small style="margin-top:35px;">
                                                    <span ng-class="data.badmintonDate.label_class" style="margin-right:5px;">{{data.badmintonDate.label_message}}</span>
                                                    Public Event
                                                    <i class="fa fa-globe" style="margin-right:5px;margin-left:5px;"></i>
                                                    Booked by <a ng-href="profile.php?id={{data.badmintonDate.creator.user_id}}">{{data.badmintonDate.creator.username}}</a>
                                                </small>
                                                <button class="btn btn-primary" style="margin-left:0px;float:right;" ng-show="!data.badmintonDate.joined && !data.badmintonDate.left" ng-click="join()">Join</button>  
                                                <button class="btn btn-warning" style="margin-left:0px;float:right;" ng-show="data.badmintonDate.joined" ng-click="notifyAbsence()" ng-disabled="data.badmintonDate.label_class == 'label label-danger'">Notify Absence</button>
                                                <button class="btn btn-success" style="margin-left:0px;float:right;" ng-show="data.badmintonDate.left" ng-click="withdrawAbsence()">Withdraw Absence</button>
                                            </div>
                                        </rd-widget-body>
                                    </rd-widget>
                                </div>
                                <div style="margin-bottom:10px;">
                                    <rd-widget>
                                        <rd-widget-body>    
                                            <div class="message">
                                                <h4 style="margin-bottom:0px;margin-top:5px;">{{data.badmintonDate.begin_date}}</h4>
                                                <small style="color:#bdc3c7;">
                                                    {{data.badmintonDate.in_about}} <i class="fa fa-circle" style="font-size:5px;padding:5px;"></i>{{data.badmintonDate.message}}
                                                </small>
                                                <hr style="margin-top:10px;margin-bottom:10px;">
                                                <h4 style="margin-bottom:0px;margin-top:0px;">University of Toronto Athletic Centre</h4>
                                                <small style="color:#bdc3c7;">
                                                    55 Harbord Street, Toronto, ON, Canada, M5S 2W6
                                                </small>
                                            </div>
                                        </rd-widget-body>
                                    </rd-widget>
                                </div>
                                <div style="margin-bottom:10px;">
                                    <rd-widget>
                                        <rd-widget-body>
                                            <div class="comment">
                                                {{data.badmintonDate.summary}}
                                            </div>
                                            <div class="comment" ng-show="!data.badmintonDate.summary">
                                                No description supplied by the host
                                            </div>
                                        </rd-widget-body>
                                    </rd-widget>
                                </div>
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
                                                    <div class="authorAvatar">
                                                       <img ng-src="{{message.author.avatar_link}}" class="img-responsive" style="width:auto;height:50px;">
                                                        <span>
                                                            <a ng-href="profile.php?id={{message.author.user_id}}">
                                                                {{message.author.username}}
                                                            </a>
                                                            <br/>
                                                            <small style="font-size:12px;color:#bdc3c7;">
                                                                {{message.moment_posted}}
                                                            </small>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <p>
                                                        {{message.message_text}}
                                                    </p>
                                                    <hr>
                                                    <form>
                                                        <textarea ng-model="thread.possible_comment" class="form-control" placeholder="Your comment..." enter-submit="postThread()">
                                                        </textarea>
                                                    </form>
                                                </div>
                                                <hr />
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
                        <div style="margin-bottom:10px;">
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
                        <rd-widget>
                            <rd-widget-header title="Participating ({{data.badmintonDate.attendees.length}})">
                            </rd-widget-header>
                            <rd-widget-body>
                                <div class="message" ng-repeat="user in data.badmintonDate.attendees" style="margin-bottom:5px;">
                                    <div class="authorAvatar">
                                        <img ng-src="{{user.avatar_link}}" style="height:50px;">
                                        <span>
                                            <a ng-href="profile.php?id={{user.user_id}}">
                                               {{user.username}}
                                            </a>
                                            <br/>
                                            <small style="font-size:12px;color:#bdc3c7;">
                                                Joined on {{user.date_joined | date:'medium'}}
                                            </small>
                                        </span>
                                    </div>
                                </div>
                                <div class="message" ng-show="data.badmintonDate.attendees.length == 0">
                                    Nobody is participating in this event yet (free court?)
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