angular./**
* app Module
*
* Description
*/
module('app', []).config(function($provide) {
	$provide.constant('getAllBadmintonDates', 'postRequests/index/getAllBadmintonDates.php');
	$provide.constant('joinBadmintonDate', 'postRequests/user/joinBadmintonDate.php');
	$provide.value('MySQLtoJS', function(datetimeString) {
		var t = datetimeString.split(/[- :]/);
		var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
		return d;
	});
}).controller('controller', ['$scope', '$http', 'getAllBadmintonDates', 'MySQLtoJS', 'joinBadmintonDate', function($scope, $http, getAllBadmintonDates, MySQLtoJS, joinBadmintonDate){
	$scope.data = {};
	
	$scope.join = function() {
		var promiseJoin = $http({
			method: "post",
			url: joinBadmintonDate,
			data: {
				date_id: $scope.data.badmintonDate.date_id
			},
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
		}).then(function(successResponse) {
			console.log(successResponse);
		}, function(errorResponse) {
			console.log(errorResponse);
		})
	}

	var promiseGetAllDates = $http({
		method: "post",
		url: getAllBadmintonDates,
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
	}).then(function(successResponse) {
		$scope.data.events = successResponse.data;
		//Conver the dates to JS
		angular.forEach($scope.data.events, function(value, key) {
			value.begin_datetime = MySQLtoJS(value.begin_datetime);
			value.end_datetime = MySQLtoJS(value.end_datetime);	
		});
		//Convert to JS objects
/*		var badmintonDates = convertMySQLToJS(successResponse.data);
		$scope.eventSources = [
			{
				color: '#f00',
       			textColor: 'yellow',
				events: badmintonDates
			}
		];
		$scope.json = angular.toJson($scope.data.badmintonDates, true);
		console.log($scope.eventSources);*/
	}, function(errorResponse) {
		console.log(errorResponse);
	});
}])