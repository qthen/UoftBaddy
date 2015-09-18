var app = angular./**
* app Module
*
* Description
*/
module('app', []).config(function($provide) {
	$provide.constant('loadUser', 'postRequests/profile/loadUser.php');
	
});

app.controller('controller', ['$scope', '$http', 'loadUser', function($scope, $http, loadUser){

	$scope.data = {},
	$scope.data.profile = {};

	$scope.init = function(user_id) {
		$scope.data.profile.user_id = user_id;
		var promiseLoadBasicProfile = $http({
			method: "post",
			url: loadUser,
			data: {
				profile_id: $scope.profile.user_id
			},
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
		});

		promiseLoadBasicProfile.then(function(successReponse) {
			$scope.data.profile = successReponse.data;
		}, function(errorResponse) {
			console.log('Error fetching user profile');
		});
	}



}]);