<?php
$user = User::get_current_user();
?>
<html ng-app="app">
	<head>
	</head>
	<body class="container-fluid" ng-controller="main" ng-init="init('<?php echo $user->user_id;?>')">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<form>
					<input type="submit" ng-click="createBadmintonDate">
					
				</form>
			</div>
		</div>
	</body>
</html>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="/bower_components/angular-bootstrap/ui-bootstrap.min.js"></script>
<script src="/angular/index/index.js"></script>