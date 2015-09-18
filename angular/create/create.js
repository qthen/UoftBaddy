var app = angular./**
* app Module
*
* Description
*/
module('app', []).config(function($provide) {
	$provide.constant('createDate', 'postRequests/user/createBadmintonDate.php');
});

app.controller('main', ['$scope', '$http', 'createDate', '$window', function($scope, $http, createDate, $window){
	$scope.data = {}; //Default holding object

	//Set some object properties
	$scope.data.user = {},
	$scope.data.form = {};

	$scope.init = function(userID) {
		$scope.data.user = {
			'user_id:' userID
		};
	}

	$scope.createBadmintonDate = function() {
		if ($scope.data.user.userID) {
			if ($scope.data.form.datetime && $scope.data.form.datename && (($scope.data.form.visibility == 1) || ($scope.data.form.visibility == 0)) && $scope.data.form.summary) {
				var promiseCreateDate = $http({
					method: "post",
					url: createDate,
					data: {
						datetime: $scope.data.form.datetime,
						datename: $scope.data.form.datename,
						summmary: $scope.dat.form.summary,
						visibility: $scope.data.form.visibility
					},
					headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
				});

				promiseCreateDate.then(function(successResponse) {
					$window.location.href = 'date.php?id=' + successResponse.data.date_id;
				})
			}
		}
	}

	
}]);