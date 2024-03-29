<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/autoload.php';
?>
<html lang="en" ng-app="RDash">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

	<title>RDash AngularJS</title>
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
  <!-- Custom Scripts -->
  <script type="text/javascript" src="js/dashboard.js"></script>
</head>
<body ng-controller="controller">
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
        <div ui-view></div>

      </div><!-- End Page Content -->
    </div><!-- End Content Wrapper -->
  </div><!-- End Page Wrapper -->
</body>
</html>