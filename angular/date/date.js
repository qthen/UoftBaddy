angular./**
* app Module
*
* Description
*/
module('app', ['ui.bootstrap'])
	.config(function($provide) {
		$provide.constant('getBadmintonDate', 'postRequests/date/getBadmintonDate.php');
		$provide.constant('getThatDayDates', 'postRequests/date/getBadmintonDateByDay.php');
		$provide.constant('getThatWeekDates', 'postRequests/date/getBadmintonDateByWeek.php');
		$provide.constant('getMessagesFromDate', 'postRequests/date/getMessagesFromDate.php');
		$provide.constant('joinBadmintonDate', 'postRequests/user/joinBadmintonDate.php');
		$provide.value('MySQLtoJS', function(datetimeString) {
			var t = datetimeString.split(/[- :]/);
			var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
			return d;
		})
	}).directive("rdWidget", function() {
	    var d={
	        transclude:!0,
	        template:'<div class="widget" ng-transclude></div>',
	        restrict:"EA"
	    };
	    return d
	}).directive("rdLoading", function() {
	    var d=
	    {
	        restrict:"AE",
	        template:'<div class="loading"><div class="double-bounce1"></div><div class="double-bounce2"></div></div>'
	    };
	    return d
	}).directive("rdWidgetBody", function() {
	    var d=
	    {
	        requires:"^rdWidget",
	        scope:{
	            loading:"@?",
	            classes:"@?"
	        },
	        transclude:!0,
	        template:'<div class="widget-body" ng-class="classes"><rd-loading ng-show="loading"></rd-loading><div ng-hide="loading" class="widget-content" ng-transclude></div></div>',
	        restrict:"E"
	    };
		return d
	}).directive("rdWidgetFooter",function (){
	    var e=
	    {
	        requires:"^rdWidget",
	        transclude:!0,
	        template:'<div class="widget-footer" ng-transclude></div>',
	        restrict:"E"
	    };
		return e
	}).directive("rdWidgetHeader", function() {
	    var e=
	    {
	        requires:"^rdWidget",
	        scope:{
	            title:"@",
	            icon:"@"},
	            transclude:!0,
	            template:'<div class="widget-header"><i class="fa" ng-class="icon"></i> {{title}} <div class="pull-right" ng-transclude></div></div>',
	            restrict:"E"
	    };
		return e
	}).controller('controller', ['$scope', '$http', 'getBadmintonDate', 'getThatDayDates', 'getThatWeekDates', 'getMessagesFromDate', 'MySQLtoJS', 'joinBadmintonDate', function($scope, $http, getBadmintonDate, getThatDayDates, getThatWeekDates, getMessagesFromDate, MySQLtoJS, joinBadmintonDate){

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

		$scope.getMessages = function(d\ate_id) {
			/*
			(Str) -> Null
			 */
			var promiseGetMessages = $http({
				method: "post",
				url: getMessagesFromDate,
				data: {
					date_id: date_id
				},
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
			}).then(function(successResponse) {
				$scope.data.messages = successResponse.data.messages;
				for (var i = 0; i < $scope.data.messages.length; i++) {
					$scope.data.messages[i].date_posted = MySQLtoJS($scope.data.messages[i].date_posted);
				}
				console.log(successResponse);
			}, function(errorResponse) {
				console.log(errorResponse);
			});
		}


		$scope.getSideBar = function(dateString) {
			/*
			(Str) -> Bool
			Fetches the sidebar of the badminton date
			 */
			var promiseGetThatDay = $http({
				method: "post",
				url: getThatDayDates,
				data: {
					date: dateString
				},
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
			}).then(function(successResponse) {
				$scope.data.thatDay = successResponse.data;
			}, function(errorResponse) {
				console.log(errorResponse);
			});

			var promiseGetThatWeek = $http({
				method: "post",
				url: getThatWeekDates,
				data: {
					date: dateString
				},
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
			}).then(function(successResponse) {
				$scope.data.thatWeek = successResponse.data;
			}, function(errorResponse) {
				console.log(errorResponse);
			});
		}

		$scope.init = function(date_id) {
			console.log(date_id);
			$scope.data = {};

			var promiseGetDate = $http({
				method: "post",
				url: getBadmintonDate,
				data: {
					date_id: date_id
				},
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
			}).then(function(successResponse) {
				console.log(successResponse);
				$scope.data.badmintonDate = successResponse.data;
				console.log($scope.data.badmintonDate);
				var dateString = $scope.data.badmintonDate.begin_datetime.split(' ')[0];
				console.log(dateString);

				console.log($scope.data.badmintonDate.date_id);

				$scope.getMessages($scope.data.badmintonDate.date_id);
				$scope.getSideBar(dateString);
			}, function(errorResponse) {
				console.log(errorResponse);
			});
		}
		
	}]);