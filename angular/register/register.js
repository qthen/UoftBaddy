var app = angular./**
* app Module
*
* Description
*/
module('app', ['ngDialog']).config(function($provide) {
	$provide.constant('attemptRegister', 'postRequests/register/register.php');
});

app.controller('controller', ['$scope', '$http', 'attemptRegister', 'ngDialog', '$window', function($scope, $http, attemptRegister, ngDialog, $window){

	$scope.data = {}; //Holding object for scope values

	$scope.register = function() {
		if ($scope.data.username && $scope.data.password && $scope.data.email && $scope.data.confirmPassword) {
			var promiseRegister = $http({
				method: "post",
				url: attemptRegister,
				data: {
					email: $scope.data.email,
					username: $scope.data.username,
					password: $scope.data.password,
					confirm_password: $scope.data.confirmPassword,
				},
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
			});

			promiseRegister.then(function(successResponse) {
				console.log(successResponse);
				var dialog = ngDialog.open({
					template: 'html/ngDialog/registed.html'
				});
				dialog.closePromise.then(function(successResponse) {
					$window.location.href = '/index.php';
				});
			}, function(errorResponse) {
				alert('Error');
				console.log(errorResponse);
			})
		}	
	}
	
}])