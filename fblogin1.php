<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/src/Facebook/autoload.php';
$fb = new Facebook\Facebook ([
    'app_id' => '1632050047043531',
    'app_secret' => '265828cd94179b3a9f5e6e786cb90202',
    'default_graph_version' => 'v2.4'
    ]);

$helper = $fb->getRedirectLoginHelper();

$permissions = ['email']; // Optional permissions
$loginUrl = $helper->getLoginUrl('http://uoftbaddy.ca/login-callback.php', $permissions);
print_r($_SESSION);

echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';
?>
<html>
	<head>
    <!-- Bootstrap Core CSS -->
    <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="/css/fblogin.css">
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12" style="color:white;text-align:center;margin-top:5%;margin-bottom:10px;">
                    <h1 style="margin-bottom:2px;font-size:60px;">
                    <i class="fa fa-university" style="margin-bottom:10px;"></i>
                    UoftBaddy</h1>
                    <small>
                        Written in PHP (server-side), MySQL (database), VanillaJavaScript, AngularJS 1.4 Web Framework.
                    </small>
                    <br/><br/><br/>
                <h5>
                    UoftBaddy is a social site aimed at finding and booking badminton courts at the Univeristy of Toronto with the ultiamte goal at improving badminton experiece and presence at the University. Come and join us!
                </h5>
            </div>
            <div class="col-md-3 col-md-offset-3" style="margin-bottom:1px;">
            	<?php echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';?>
                <a href="<?php echo htmlspecialchars($loginUrl);?>" class="btn btn-block btn-social btn-facebook">
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