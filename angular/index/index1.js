var app = angular./**
* app Module
*
* Description
*/
module('app', ['ui.bootstrap', 'ui.calendar', 'ngDialog'])
	.config(function($provide) {
		$provide.constant('createConfirmedTime', 'html/ngDialog/create_date.html');
		$provide.constant('createConfirmedTimePHP', 'postRequests/user/createBadmintonDate.php');
		$provide.constant('loadBasicUser', 'postRequests/profile/loadUser.php');
		$provide.constant('createTentativeDate', 'html/ngDialog/create_tentative_date.html');
		$provide.constant('markUnavailable', 'html/ngDialog/mark_unavailable_date.html');
		$provide.constant('getAllBadmintonDates', 'postRequests/index/getAllBadmintonDates.php');
		$provide.constant('loadTopBar', 'postRequests/index/getTopBar.php');
		$provide.constant('loadCurrentUser', 'postRequests/user/loadCurrentUser.php');
		$provide.constant('getUserDropdown', 'postRequests/user/getUserDropdown.php');
		$provide.constant('getUserStats', 'postRequests/index/getUserStats.php');

		//Notifs providers
		$provide.constant('getUserNotifications', 'postRequests/user/getUserNotifications.php');

		$provide.value('duringHours', function(dateInput) {
			if (angular.isDate(dateInput)) {
				var hours = dateInput.getHours();
				return ((hours > 7) && (hours < 23));
			}
			else {
				return false;
			}
		});
	}).service('uiCalendarFormat', function() {
		this.eventSourcing = function(dateObject) {
			var t = dateObject.begin_datetime.split(/[- :]/);
			var start = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
			var t = dateObject.end_datetime.split(/[- :]/);
			var end = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
			return {
				title: dateObject.datename,
				start: start,
				end: end
			};
		}
	}).service('httpHandler', ['$http', '$q', function($http, $q){
        /*
        Dynamicallly Send HTTP request
         */
        this.request = function(url, data) {
            /*
            (String, JSON) -> Promise Object
             */
            var deferred = $q.defer();

            if (url && angular.isObject(data) && !Array.isArray(data)) {
                var httpRequest = $http({
                    method: "post",
                    url: url,
                    data: data,
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                });
                return httpRequest;
            }
            else {  
                deferred.reject('The input parameters are not valid');
            }
            return deferred.promise;    
        }
    }]).factory('indexFactory', ['httpHandler', 'getUserStats', function(httpHandler, getUserStats){
    	return {
    		UsersByHostedEvents: function() {
    			/*
    			(Null) -> Promise
    			*/
    			return httpHandler.request(getUserStats, {});
    		}
    	}
    }]).factory('notificationFactory', ['httpHandler', 'getUserNotifications', function(httpHandler, getUserNotifications){
    	return {
    		notificationsPromise: function() {
    			/*
    			(Null) -> Promise
    			*/
    			return httpHandler.request(getUserNotifications, {
    			});
    		}
    	}
    }]).factory('userDropdown', ['httpHandler', 'getUserDropdown', function(httpHandler, getUserDropdown){
        return {
            dropdownFields: function() {
                /*
                (Null) -> Promise Object
                Gets the fields for the user dropdown at the site
                */
                return httpHandler.request(getUserDropdown, {
                });
            }
        }
    }]).directive("rdWidget", function() {
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
    }).controller('controller', ['$scope', '$http', 'ngDialog', 'createConfirmedTime', 'createConfirmedTimePHP', 'loadBasicUser', '$q', 'createTentativeDate', 'markUnavailable', 'getAllBadmintonDates', 'uiCalendarConfig', 'duringHours', 'loadTopBar', 'loadCurrentUser', 'userDropdown', 'notificationFactory', 'uiCalendarFormat', 'indexFactory', function($scope, $http, ngDialog, createConfirmedTime, createConfirmedTimePHP, loadBasicUser, $q, createTentativeDate, markUnavailable, getAllBadmintonDates, uiCalendarConfig, duringHours, loadTopBar, loadCurrentUser, userDropdown, notificationFactory, uiCalendarFormat, indexFactory) {
	$scope.data = {}, //Holding object for scopes
	$scope.data.animationsEnabled = true;
	$scope.data.badmintonDates = [
	];

	$scope.toggle = true;

	//Get the user notifications
	notificationFactory.notificationsPromise().then(function(successResponse) {
		$scope.data.notifications = successResponse.data;
	}, function(errorResponse) {
		console.log(errorResponse);
	});

	var promiseGetUserDropdown = userDropdown.dropdownFields().then(function(successResponse) {
		$scope.data.dropdown = {
			user: successResponse.data.user,
			fields: successResponse.data.fields
		}
		console.log(successResponse);
	}, function(errorResponse) {
		console.log(errorResponse);
	});

	//Get some stats
	indexFactory.UsersByHostedEvents().then(function(successResponse) {
		$scope.data.usersByHostedEvents = successResponse.data;
	}, function(errorResponse) {
		console.log(errorResponse);
	});
	

	$scope.loadTopBar = function() {
		var promiseLoadTopBar = $http({
			method: "post",
			url: loadTopBar,
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
		}).then(function(successResponse) {
			console.log(successResponse);	
			$scope.data.TopBar = successResponse.data;
		}, function(errorResponse) {
			console.log(errorResponse);
		})
	}

	$scope.loadTopBar();
	
	$scope.toggleSidebar = function() {
		/*
		Had to simplify a lot of routing
		 */
		$scope.toggle = !$scope.toggle;
	}


	var promiseGetAllDates = $http({
		method: "post",
		url: getAllBadmintonDates,
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
	}).then(function(successResponse) {
		console.log(successResponse);
		//Convert to JS objects
		var badmintonDates = successResponse.data;
		$scope.data.badmintonDates = [];
		for (var i = 0; i < badmintonDates.length; i++) {
			$scope.data.badmintonDates.push(uiCalendarFormat.eventSourcing(badmintonDates[i]));
		}
		$scope.eventSources = [
			{
				color: '#f00',
       			textColor: 'yellow',
				events: $scope.data.badmintonDates
			}
		];
		$scope.json = angular.toJson($scope.data.badmintonDates, true);
		console.log($scope.eventSources);
	}, function(errorResponse) {
		console.log(errorResponse);
	});

	$scope.changeView = function(view) {
		uiCalendarConfig.calendars.main.fullCalendar('changeView', view);
	};

	$scope.init = function(userID) {
		$scope.userPromise = $q.defer();

		$scope.user = {
			'user_id': userID
		}

		if ($scope.user.user_id) {
			var promiseGetUser = $http({
				method: "post",
				url: loadBasicUser,
				data: {
					profile_id: $scope.user.user_id
				},
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
			});

			var promiseChain = promiseGetUser.then(function(successResponse) {
				$scope.user = successResponse.data;
			}, function(errorResponse) {
				$scope.user = {
					'user_id': null
				}
			});

			promiseChain.then(function() {
				$scope.userPromise.resolve();
			}, function() {
				$scope.userPromise.resolve();
			});
		}
		else {
			$scope.userPromise.resolve();
		}
	}

	$scope.createDateDialog = function() {
		$scope.userPromise.promise.then(function(successResponse) {
			if ($scope.user.user_id) {
			ngDialog.open({
					template: createConfirmedTime,
					controller: ['$scope', '$http', 'ngDialog', 'createConfirmedTimePHP', 'duringHours', function($scope, $http, ngDialog, createConfirmedTimePHP, duringHours){

						$scope.data = {}; //Scope holding object for primitive scope

						//Defaults for form code
						$scope.data.eventName = 'Badminton Date',
						$scope.data.dt = new Date(),
						$scope.data.mytime = new Date();
						$scope.data.endtime = new Date();

						//Code to submit the form
						$scope.submit = function() {

							if (duringHours($scope.data.mytime) && duringHours($scope.data.endtime)) {
								var dateEventMySQL = $scope.data.dt.toISOString().substring(0, 10)
								var endTimeMySQL = $scope.data.endtime.getHours();
								var startTimeMySQL = $scope.data.mytime.getHours();

								startTimeMySQL = dateEventMySQL + ' ' + startTimeMySQL + ':00:00'; 
								endTimeMySQL = dateEventMySQL  + ' ' + endTimeMySQL + ':00:00'; 
								console.log(dateEventMySQL);
								console.log(startTimeMySQL);
								console.log(endTimeMySQL);
								console.log($scope.data.eventName);

								var promise = $http({
									method: "post",
									url: createConfirmedTimePHP,
									data: {
										begin_datetime: startTimeMySQL,
										end_datetime: endTimeMySQL,
										datename: $scope.data.eventName
									},
									headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
								});

								promise.then(function(successResponse) {
									ngDialog.open({
										template: 'html/ngDialog/success_create_confirm_date.html'
									});
								}, function(errorResponse) {
									console.log(errorResponse);
								});
							}
							else {
								$scope.data.errorMessage = 'Dates are not during the opening hours of athletic facilities of UofT (7am - 11pm)';
							}
						}

						//Timepicker code
						$scope.mytime = new Date(),
						$scope.endtine = new Date();

						$scope.hstep = 1;


						//Datepicker code
						$scope.today = function() {
							$scope.dt = new Date();
						}

						$scope.today(); //Set the default date to today

						$scope.minDate = function() {
							$scope.minDate = new Date(); //Default is today
						}

						$scope.minDate(); //Set the defualt min date to today

						$scope.status = {
							opened: false
						};

						$scope.open = function($event) {
							$scope.status.opened = true;
						};

						$scope.format = 'yyyy/MM/dd';

					}]
				});
			}
			else {
				ngDialog.open({
					template: 'html/ngDialog/login_action.html'
				});
			}
		});
	}
}]);
