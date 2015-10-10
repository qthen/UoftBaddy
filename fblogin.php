<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/Facebook/autoload.php';
$fb = new Facebook\Facebook ([
    'app_id' => '----',
    'app_secret' => '-----',
    'default_graph_version' => 'v2.4'
    ]);

$helper = $fb->getRedirectLoginHelper();

$permissions = ['email']; // Optional permissions
$loginUrl = $helper->getLoginUrl('http://uoftbaddy.ca/login-callback.php', $permissions);
//print_r($_SESSION);

//echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';
?>
<html> 
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>The UofTBaddy Project</title>

    <!-- Bootstrap Core CSS -->
    <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="bower_components/bootstrap-social/bootstrap-social.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="bower_components/startbootstrap-sb-admin-2/dist/css/sb-admin-2.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <link href="bower_components/ngDialog/css/ngDialog.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="/css/fblogin.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12" style="color:white;text-align:center;margin-top:5%;margin-bottom:10px;">
            <?php //print_r($_SESSION);?>
            <?php //echo "<a href=\"" . htmlspecialchars($loginUrl) . "\">login</a>";?>
                    <h1 style="margin-bottom:2px;font-size:60px;">
                    <i class="fa fa-university" style="margin-bottom:10px;"></i>
                    UoftBaddy</h1>
                    <small>
                        Written in PHP (server-side), MySQL (database), VanillaJavaScript, AngularJS 1.4 Web Framework.
                    </small>
                    <br/><br/><br/><br/><br/>
                <p>
                    Welcome to UoftBaddy! This is a social site aimed at finding players and booking badminton courts at the Univeristy of Toronto.
                    <br/>
                    Whether you're a casual player, Varsity-level, or just started playing yesterday, we're glad you're here. Come and join us!
                </p>
            </div>
            <div class="col-md-3 col-md-offset-3" style="margin-bottom:1px;">
                <a class="btn btn-block btn-social btn-facebook" href="<?php echo htmlspecialchars($loginUrl);?>">
                    <i class="fa fa-facebook"></i> Log in with Facebook
                </a>
            </div>
            <div class="col-md-3" style="margin-bottom:1px;">
                <a class="btn btn-block btn-social btn-linkedin" disabled>
                    <i class="fa fa-university"></i> Log in with Utoronto Mail
                </a>
            </div>
            <div class="col-md-12" style="color:white;text-align:center;">
                <small>
                    *Currently UoftBaddy only supports log in with Facebook for now and will add Utoronto Mail and other account confirmatons in the future
                </small>
            </div>
        </div>
    </div>
    <div class="col-md-12" style="color:black;position:absolute;bottom:0;">
        <small>
         Dependencies on Bootstrap, Angular-Boostrap, angularMoment, RDash, RDash-Angular
            UoftBaddy is not associated in any way with the University of Toronto, The Faculty of Kinesiology and Physical Education, or related departmens and sectors of the University. This is not a service offered by them. You can contact uoftbaddy@gmail.ca. 
        </small>
    </div>

</body>
</html>

<?php
//print_r($_SESSION);
?>
