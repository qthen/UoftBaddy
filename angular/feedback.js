var app = angular./**
* app Module
*
* Description
*/
module('app', ['ui.bootstrap', 'ui.calendar', 'ngDialog', 'angularMoment', 'angularSmoothscroll', 'ngScrollSpy'])
	.config(function($provide) {
		$provide.constant('createConfirmedTime', 'html/ngDialog/create_date.html');
		$provide.constant('createConfirmedTimePHP', 'postRequests/user/createBadmintonDate.php');
		$provide.constant('loadBasicUser', 'postRequests/profile/loadUser.php');
		$provide.constant('createTentativeDate', 'html/ngDialog/create_tentative_date.html');
		$provide.constant('markUnavailable', 'html/ngDialog/mark_unavailable_date.html');
		$provide.constant('getAllBadmintonDates', 'postRequests/index/getAllBadmintonDates.php');
		$provide.constant('getThreads', 'postRequests/index/getAllFutureThreads.php');
		$provide.constant('postThreadPHP', 'postRequests/user/postThread.php');
		$provide.constant('postCommentPHP', 'postRequests/user/postThreadComment.php');

		$provide.constant('getThreadsPage', 'postRequests/index/getThreadsPage.php');

		$provide.constant('getUserDropdown', 'postRequests/user/getUserDropdown.php');

		$provide.constant('getThreadComments', 'postRequests/thread/getThreadComments.php');
		$provide.constant('getThreadPlayers', 'postRequests/thread/getThreadPlayers.php');
		$provide.constant('getIndexStats', 'postRequests/index/getBadmintonDateStats.php');
		$provide.constant('getCourtsStats', 'postRequests/index/getCourtStats.php');
		$provide.constant('getNumberOfPeopleLookingToPlay', 'postRequests/index/getNumberOfPeopleLookingToPlay.php');
		$provide.constant('getUserNotifications', 'postRequests/user/getUserNotifications.php');

		$provide.constant('sendFeedback', 'postRequests/user/mailFeedback.php');

		$provide.value('convertMySQLToJS', function(arrayInput) {
			for (var i = 0; i < arrayInput.length; i++) {
				var t = arrayInput[i].begin_datetime.split(/[- :]/);
				var start = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
				var t = arrayInput[i].end_datetime.split(/[- :]/);
				var end = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
				var title = arrayInput[i].datename;
				arrayInput[i] = {
					start: start,
					end: end,
					title: title
				};
			}
			return arrayInput;
		});

		$provide.value('duringHours', function(dateInput) {
			if (angular.isDate(dateInput)) {
				var hours = dateInput.getHours();
				return ((hours > 7) && (hours < 23));
			}
			else {
				return false;
			}
		});
	}).service('serviceDate', function() {
        this.MySQLDatetimeToDateObject = function(MySQLDatetimeString) {
            var t = MySQLDatetimeString.split(/[- :]/);
            return new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
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
    }]).factory('feedbackFactory', ['httpHandler', 'sendFeedback', function(httpHandler, sendFeedback){
    	return {
    		sendFeedback: function(feedbackObject) {
    			/*
    			(FeedbackObject) -> Promise Object
    			*/
    			return httpHandler.request(sendFeedback, feedbackObject);
    		}
    	}
    }]).factory('notificationsFactory', ['httpHandler', 'getUserNotifications', function(httpHandler, getUserNotifications) {
    	return {
    		CurrentUserNotifications: function() {
    			return httpHandler.request(getUserNotifications, {});
    		}
    	}
    }]).service('threadService', ['httpHandler', 'postThreadPHP', 'moment', function(httpHandler, postThreadPHP, moment){
    	this.postThread = function(threadObject) {
    		/*
    		(String) -> Promise
    		*/
    		if (threadObject.date_play) {
    			var date_play = moment(threadObject.date_play).format('YYYY-MM-DD');
    		}
    		else {
    			var date_play = null;
    		}
    		console.log(threadObject);
    		return httpHandler.request(postThreadPHP, {
    			thread_text: threadObject.thread_text,
    			date_play: date_play,
    			type: threadObject.type
    		});
    	}
    }]).factory('indexFactory', ['httpHandler', 'getIndexStats', 'getCourtsStats', 'getNumberOfPeopleLookingToPlay', function(httpHandler, getIndexStats, getCourtStats, getNumberOfPeopleLookingToPlay){
    	return {
    		SiteStats: function() {
    			/*
    			(Null) -> Promise
    			*/
    			return httpHandler.request(getIndexStats, {});	
    		},
    		CourtStats: function() {
    			/*
    			(Null) -> Promise
    			*/
    			return httpHandler.request(getCourtStats, {});
    		},
    		LookingToPlay: function() {
    			/*
    			(Null) -> Promise
    			*/
    			return httpHandler.request(getNumberOfPeopleLookingToPlay, {});
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
    }]).factory('threadHelper', ['serviceDate', 'moment', function(serviceDate, moment){
    	return {
    		threadMySQLFieldstoJS: function(threadObject) {
    			console.log(threadObject);
    			threadObject.date_play = serviceDate.MySQLDatetimeToDateObject(threadObject.date_play + ' 00:00:00');
    			threadObject.date_posted = serviceDate.MySQLDatetimeToDateObject(threadObject.date_posted);
    			console.log(threadObject);
    			return threadObject
    		}
    	};
    }]).factory('threadFactory', ['httpHandler', 'getThreadComments', 'serviceDate', 'getThreadPlayers', 'getThreadsPage', 'getThreads', function(httpHandler, getThreadComments, serviceDate, getThreadPlayers, getThreadsPage, getThreads){
    	return {
    		threadComments: function(threadObject) {
    			/*
    			(ThreadObject) -> Array of Comments
    			*/
    			httpHandler.request(getThreadComments, {
    				thread_id: threadObject.thread_id
    			}).then(function(successResponse) {
    				var threadComments = successResponse.data;
    				var threadCommentsArray = [];
    				angular.forEach(threadComments.comments, function(value, key) {
    					value.date_posted = serviceDate.MySQLDatetimeToDateObject(value.date_posted);
    					this.push(value);
    				}, threadCommentsArray);
    				return threadCommentsArray;
    			}, function(errorResponse) {
    				console.log(errorResponse);
    			});
    		},
    		allThreadPlayers: function() {
    			/*
    			(Null) -> Promise Object
    			*/
    			return httpHandler.request(getThreadPlayers, {

    			});
    		},
    		getThreads: function() {
    			/*
    			(Null) -> Promise Object
    			*/
    			return httpHandler.request(getThreadsPage, {});
    		},
    		getFutureLooingToPlay: function() {
    			/*
    			(Null) -> Promise
    			*/
    			return httpHandler.request(getThreads, {});
    		}
    	};
    }]).directive("rdWidget", function() {
        var d={
            transclude:!0,
            template:'<div class="widget" ng-transclude></div>',
            restrict:"EA"
        };
        return d
    }).directive('enterSubmit', function () {
	    return {
	      restrict: 'A',
	      link: function (scope, elem, attrs) {
	       
	        elem.bind('keydown', function(event) {
	          var code = event.keyCode || event.which;
	                  
	          if (code === 13) {
	            if (!event.shiftKey) {
	              event.preventDefault();
	              scope.$apply(attrs.enterSubmit);
	            }
	          }
	        });
	      }
	    }
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
    }).controller('controller', ['$scope', '$http', 'ngDialog', 'createConfirmedTime', 'createConfirmedTimePHP', 'loadBasicUser', '$q', 'createTentativeDate', 'markUnavailable', 'getAllBadmintonDates', 'convertMySQLToJS', 'uiCalendarConfig', 'duringHours', 'getThreads', 'moment', 'postThreadPHP', 'postCommentPHP', 'userDropdown', 'threadHelper', 'threadFactory', 'threadService', 'indexFactory', '$window', 'notificationsFactory', 'feedbackFactory', function($scope, $http, ngDialog, createConfirmedTime, createConfirmedTimePHP, loadBasicUser, $q, createTentativeDate, markUnavailable, getAllBadmintonDates, convertMySQLToJS, uiCalendarConfig, duringHours, getThreads, moment, postThreadPHP, postCommentPHP, userDropdown, threadHelper, threadFactory, threadService, indexFactory, $window, notificationsFactory, feedbackFactory) {
	$scope.data = {}, //Holding object for scopes
	$scope.data.animationsEnabled = true;
	$scope.data.thread_title = ''; //By default
	$scope.data.thread_text = '';

	$scope.data.view = 1; //By default
	$scope.data.anonymous = 1;

	$scope.submitFeedback = function() {
		if ($scope.data.message) {
			if ($scope.data.anonymous == 1) {
				var message = {
					username: 'Anonyous',
					message: $scope.data.message
				}
			}
			else {
				var message = {
					username: $scope.user.username,
					message: $scope.data.message
				}
			}
			console.log(message);
			feedbackFactory.sendFeedback(message).then(function(successResponse) {
				console.log(successResponse);
				var dialog = ngDialog.open({
					template: 'html/ngDialog/send_feedback.html'
				});
				dialog.closePromise.then(function() {
					$window.location.reload();
				});
			}, function(errorResponse) {
				console.log(errorResponse);
			});
		}
	}

	notificationsFactory.CurrentUserNotifications().then(function(successResponse) {
		console.log(successResponse);
		$scope.data.notifications = successResponse.data;
	}, function(errorResponse) {
		console.log(errorResponse);
	});
	$scope.data.doNotAsk = false; //Do not ask if you are looking to play

	threadFactory.getThreads().then(function(successResponse) {
		var allThreads = successResponse.data;
		$scope.data.allThreads = [];

		//Now loop through all the threads and convert their MySQL Date fields to JS Date objects
		for (var i = 0; i < allThreads.length; i++) {
			$scope.data.allThreads.push(threadHelper.threadMySQLFieldstoJS(allThreads[i]));
		}
		console.log($scope.data.allThreads);	

	}, function(errorResponse) {
		console.log(errorResponse);
	})

	$scope.joinThread = function(thread_id) {
		actionFactory.joinThread(thread_id).then(function(successResponse) {
			console.log(successResponse);
		}, function(errorResponse) {
			console.log(errorResponse);
		});
	}

	indexFactory.SiteStats().then(function(successResponse) {
		$scope.data.stats = successResponse.data;
		console.log(successResponse);	
	}, function(errorResponse) {
		console.log(errorResponse);
	});

	indexFactory.CourtStats().then(function(successResponse) {
		console.log(successResponse);
		$scope.data.courtsToday = parseInt(successResponse.data.today);
		$scope.data.courtsTomorrow = parseInt(successResponse.data.tomorrow);
	}, function(errorResponse) {
		console.log(errorResponse);
	});

	indexFactory.LookingToPlay().then(function(successResponse) {
		$scope.data.NumberOfPeopleLookingToPlay = successResponse.data.looking_to_play;
	}, function(errorResponse) {
		console.log(errorResponse);
	})

	$scope.$watch('data.thread_text', function(newValue, oldValue) {
		if (!$scope.data.doNotAsk) {
			if ($scope.data.thread_text && $scope.data.thread_text.length > 2) {
				$scope.data.ask = true;
			}
		}
	});

	//Get the user dropdown
    $scope.getUserDropdown = function() {
        userDropdown.dropdownFields().then(function(successResponse) {
            //console.log(successResponse);
            $scope.data.dropdown = {
                user: successResponse.data.user,
                fields: successResponse.data.fields
            };
            console.log($scope.data.dropdown);
        }, function(errorResponse) {
            console.log(errorResponse);
        });
    }
    $scope.getUserDropdown(); //Get the user dropdown

	$scope.conditionallyJoinThread = function(thread_id) {

	}

	$scope.createConditionals = function(thread_id) {
	}

	$scope.postComment = function(thread_id) {
		var loopBool = {
			looping: true
		};

		function sendRequest (iteration, value, thread_id) {
			var deferred = $q.defer();

			if (value.thread_id == thread_id) {
				$http({
					method: "post",
					url: postCommentPHP,
					data: {
						thread_id: value.thread_id,
						comment_text: value.possible_comment,
						parent_id: null
					},
					headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
				}).then(function(successResponse) {
					deferred.resolve();
					console.log(successResponse);
				}, function(errorResponse) {
					deferred.reject('With reason...');
					console.log(errorResponse);
				});
			}
			else {
				deferred.reject();
			}
			return deferred.promise;
		}

		angular.forEach($scope.data.threads, function(value, key) {
			if (this.looping) {
				for (var i = 0; i < value.length; i++) {
					if (this.looping) {
						sendRequest(i, value[i], thread_id).then(function(successResponse) {
							this.looping = false;
						}, function(errorResponse) {
							//Do nothing
						});
						if (value[i].thread_id == thread_id) {
							value[i].possible_comment = '';
						}
					}
				}
			}
		}, loopBool);
	}

	$scope.$watch('data.dt', function(newValue, oldValue) {
		console.log(newValue);
		if (Object.prototype.toString.call(newValue) === "[object Date]") {
			console.log('hi');
			//if (Date.parse($scope.data.thread_title) || (!$scope.data.thread_title) || ($scope.data.thread_title.length < 3)) {
				$scope.data.thread_title = 'Badminton - ' + moment(newValue).format('MMMM Do YYYY');
			//}
		}
	});

	// $scope.postThread = function() {
	// 	//var dateInMySQL = convertMySQLToJS($scope.data.dt);
	// 	var dateInMySQL = $scope.data.dt.toISOString().substring(0, 10)
	// 	console.log(dateInMySQL);
	// 	console.log($scope.data.thread_title);
	// 	console.log($scope.data.thread_text);
	// 	var promisePostThread = $http({
	// 		method: "post",
	// 		url: postThreadPHP,
	// 		data: {
	// 			thread_title: $scope.data.thread_title,
	// 			thread_text: $scope.data.thread_text,
	// 			date_play: dateInMySQL,
	// 			type: 1
	// 		},
	// 		headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
	// 	}).then(function(successResponse) {
	// 		console.log(successResponse);
	// 		$scope.data.thread_title = '',
	// 		$scope.data.thread_text = '',
	// 		$scope.data.dt = new Date();
	// 	}, function(errorResponse) {
	// 		console.log(errorResponse);
	// 	})
	// }

	function PossibleThread(thread_text, date_play, type) {
		this.thread_text = thread_text;
		this.date_play = date_play;
		this.type = type;
	}

	$scope.postThread = function() {
		if ($scope.data.showLookingToPlay) {
			$scope.data.type = 1;
		}
		else {
			$scope.data.type = 2;
		}
		var PossibleThreadObject = new PossibleThread($scope.data.thread_text, $scope.data.dt, $scope.data.type);
		threadService.postThread(PossibleThreadObject).then(function(successResponse) {
			console.log(successResponse);
			$scope.data.thread_title = '',
			$scope.data.thread_text = '',
			$scope.data.dt = new Date();
		}, function(errorResponse) {
			console.log(errorResponse);
		});
	}

	//Defaults for form code
	$scope.data.eventName = 'Badminton Date',
	$scope.data.dt = new Date(),
	$scope.data.mytime = new Date();
	$scope.data.endtime = new Date();

	//Datepicker code
	$scope.today = function() {
		$scope.dt = new Date();
	}

	$scope.today(); //Set the default date to today

	$scope.minDate = function() {
		$scope.minDate = new Date(); //Default is today
	}

	$scope.minDate(); //Set the defualt min date to today

	$scope.datepicker = {
		opened: false
	};

	$scope.open = function($event) {
		console.log('logged');
    	$event.preventDefault();
    	$event.stopPropagation();
		$scope.datepicker.opened = true;
	};

	$scope.format = 'yyyy/MM/dd';

	$scope.toggle = true;
	
	$scope.toggleSidebar = function() {
		/*
		Had to simplify a lot of routing
		 */
		$scope.toggle = !$scope.toggle;
	}

	// threadFactory.getThreads().then(function(successResponse) {
	// 	console.log(successResponse);
	// 	$scope.data.allThreads = successResponse.data;
	// }, function(errorResponse) {
	// 	console.log(errorResponse);
	// 	$scope.data.allThreads = [];
	// });

	//Get all the looking to play threads
	threadFactory.getFutureLooingToPlay().then(function(successResponse) {
		var threads = successResponse.data;
		$scope.data.totalThreads = 0;
		$scope.data.threads = {}; //The holding array for the threads

		//Format the threads and get the total
		angular.forEach(threads, function(value, key) {
			for (var i = 0; i < value.length; i++) {
				value[i] = threadHelper.threadMySQLFieldstoJS(value[i]);
				value[i].possible_comment = ''; //For any possible comments

				//Get the comments of this thread
				value[i].comments = threadFactory.threadComments(value[i]);
				$scope.data.totalThreads++;
			}
			this[key] = value;
		}, $scope.data.threads);
		console.log($scope.data.threads);
	}, function(errorResponse) {
		console.log(errorResponse);
	})

	// var promiseGetThreads = $http({
	// 	method: "post",
	// 	url: getThreads,
	// 	headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
	// }).then(function(successResponse) {
	// 	console.log(successResponse);
	// 	$scope.data.totalThreads = 0;
	// 	var threads = successResponse.data;
	// 	$scope.data.threads = {}; //The holding array for the threads

	// 	//Format the threads and get the total
	// 	angular.forEach(threads, function(value, key) {
	// 		for (var i = 0; i < value.length; i++) {
	// 			value[i] = threadHelper.threadMySQLFieldstoJS(value[i]);
	// 			value[i].possible_comment = ''; //For any possible comments

	// 			//Get the comments of this thread
	// 			value[i].comments = threadFactory.threadComments(value[i]);
	// 			$scope.data.totalThreads++;
	// 		}
	// 		this[key] = value;
	// 	}, $scope.data.threads);
	// 	console.log($scope.data.threads);
	// }, function(errorResponse) {
	// 	console.log(errorResponse);
	// });


	var promiseGetAllDates = $http({
		method: "post",
		url: getAllBadmintonDates,
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
	}).then(function(successResponse) {
		//Convert to JS objects
		var badmintonDates = convertMySQLToJS(successResponse.data);
		$scope.eventSources = [
			{
				color: '#f00',
       			textColor: 'yellow',
				events: badmintonDates
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
				console.log($scope.user);
			}, function(errorResponse) {
				$scope.user = {
					'user_id': nulls
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
	$scope.register = function() {
		$window.location.href = '/register.php';
	}

	$scope.login = function() {
		$window.location.href = '/fblogin.php';
	}
}]);
