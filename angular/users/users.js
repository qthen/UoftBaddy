angular./**
* app Module
*
* Description
*/
module('app', []).config(function($provide) {
	$provide.constant('getAllUsers', 'postRequests/user/getAllUsers.php');
}).controller('controller', ['$scope', '$http', 'getAllUsers', function($scope, $http, getAllUsers){
	$scope.data = {};

	var promiseGetAllUsers = $http({
		method: "post",
		url: getAllUsers,
		data: {
			filter: 'reputation'
		},
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
	}).then(function(successResponse) {
		console.log(successResponse);
		$scope.data.users = successResponse.data;
	}, function(errorResponse) {
		console.log(errorResponse);
	})
}])