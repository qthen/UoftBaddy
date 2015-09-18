    <?php
session_start();
function __autoload($class) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/$class.php";
}
$user = User::get_current_user();
if ($user->user_id) {
	//This user is already logged in
}
?>
<html lang="en" ng-app="app">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>UofT Baddy</title>

    <!-- Bootstrap Core CSS -->
    <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="bower_components/startbootstrap-sb-admin-2/dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <link href="bower_components/ngDialog/css/ngDialog.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body ng-controller="controller">
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <img src="images/University_of_Toronto.png" style="height:100px;width:auto;display:block;margin-left:auto;margin-right:auto;"/>
           	</div>
        	<div class="col-md-12">
        		<h5 style="display:inline-block;text-align:center;">
        			UofT Baddy is a service for students or related at University of Toronto for improving the allocation of badminton courts (and possibly other sports). You need to be a UofT student to sign up. 
        		</h5>
        	</div>
            <div class="col-md-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Login with Facebook</h3>
                    </div>
                    <div class="panel-body">
                        <form role="form">
                            <fieldset>
                                <input type="submit" ng-click="register()" class="btn btn-lg btn-success btn-block" value="Login with Facebook">
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        	<div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Register</h3>
                    </div>
                    <div class="panel-body">
                        <form role="form">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" placeholder="University of Toronto E-mail" name="email" type="email" autofocus ng-model="data.email">
                                </div>
                                <div class="form-group">
                                	<input class="form-control" placeholder="Desired " name="username" type="text" ng-model="data.username">
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Password" name="password" type="password" ng-model="data.password">
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Confirm Password" name="password" type="password" ng-model="data.confirmPassword">
                                </div>
                                <!-- Change this to a button or input when using this as a form -->
                                <input type="submit" ng-click="register()" class="btn btn-lg btn-success btn-block">
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="bower_components/angular-bootstrap/ui-bootstrap.min.js"></script>
<script src="bower_components/ngDialog/js/ngDialog.js"></script>
<script src="angular/register/register.js"></script>
<!-- jQuery -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- Metis Menu Plugin JavaScript -->
<script src="bower_components/metisMenu/dist/metisMenu.min.js"></script>

<!-- Custom Theme JavaScript -->
<script src="bower_components/startbootstrap-sb-admin-2/dist/js/sb-admin-2.js"></script>
