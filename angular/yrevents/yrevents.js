angular./**
* app Module
*
* Description
*/
module('app', []).
	config(function($provide) {
		$provide.constant('getUserEvents', 'postRequests/user/getUserEvents.php');
		$provide.constant('getCreatedEvents', 'postRequests/user/getCreatedUserEvents.php');
		$provide.value('MySQLtoJS', function(datetimeString) {
			var t = datetimeString.split(/[- :]/);
			var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
			return d;
		});
	}).controller('controller', ['$scope', '$http', 'getUserEvents', 'getCreatedEvents', 'MySQLtoJS', function($scope, $http, getUserEvents, getCreatedEvents, MySQLtoJS){
		$scope.init = function(userID) {
			$scope.data = {};

			$scope.user = {};
			$scope.user.user_id = userID;

			if ($scope.user.user_id) {
				var promiseGetUserEvents = $http({
					method: "post",
					url: getUserEvents,
					data: {
						user_id: $scope.user.user_id
					},
					headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
				});

				promiseGetUserEvents.then(function(successResponse) {
					console.log(successResponse);
					$scope.data.yrevents = successResponse.data;
					$scope.data.filteredEvents = successResponse.data;

					$scope.data.hostedByYou = [];
					$scope.data.hostedByOthers = [];

					//Now figure out where the current user is a creator
					for (var i = 0; i < $scope.data.events; i++) {
						$scope.data.events[i].begin_datetime = MySQLtoJS($scope.data.events[i].begin_datetime),
						$scope.data.events[i].end_datetime = MySQLtoJS($scope.data.events[i].end_datetime),
						$scope.data.events[i].leave_deadline = new Date(MySQLtoJS($scope.data.events[i].begin_datetime).getTime() - (24 * 60 * 60 * 1000));
						if ($scope.data.events[i].creator.user_id == $scope.user.user_id) {
							$scope.data.events[i].created = true;
							$scope.data.hostedByYou.push($scope.data.events[i]);
						}
						else {
							$scope.data.events[i].created = false;
							$scope.data.hostedByOthers.push($scope.data.events[i]);
						}
					}
				}, function(errorResponse) {
					alert('Error fetching your events');
				});
			}
			else {
				alert('Not logged in');
			}
		}

		$scope.changeView = function(view) {
			if (view == 'self') {
				//Hosted by the current user
				$scope.data.filteredEvents = $scope.data.hostedByYou;
			}
			else if (view == 'joined') {
				$scope.data.filteredEvents = $scope.data.hostedByOthers;
			}
			else {
				$scope.data.filteredEvents = $scope.data.yrevents;
			}
		}
	}]);