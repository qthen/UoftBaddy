var app = angular./**
* app Module
*
* Description
*/
module('app', ['ui.bootstrap', 'ui.calendar', 'ngDialog', 'angularMoment', 'smoothScroll', 'ngScrollSpy', 'ngSanitize'])
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
		$provide.constant('deleteThreadComment', 'postRequests/user/deleteThreadComment.php');

		$provide.constant('getThreadComments', 'postRequests/thread/getThreadComments.php');
		$provide.constant('getThreadPlayers', 'postRequests/thread/getThreadPlayers.php');
		$provide.constant('getNumberOfUsers', 'postRequests/index/getNumberOfUsers.php');
		$provide.constant('getFormatedTabularSchedule', 'postRequests/index/getFormatedFutureThreads.php');
		$provide.constant('getUserNotifications', 'postRequests/user/getUserNotifications.php');
		$provide.constant('getRandomUsers', 'postRequests/index/getRandomUsers.php');
		$provide.constant('markNotificationAsRead', 'postRequests/user/MarkNotificationAsRead.php');

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
    }]).factory('notificationsFactory', ['httpHandler', 'getUserNotifications', 'markNotificationAsRead', function(httpHandler, getUserNotifications, markNotificationAsRead) {
    	return {
    		CurrentUserNotifications: function() {
    			return httpHandler.request(getUserNotifications, {});
    		},
    		MarkAsRead: function(notificationObject) {
    			/*
    			(Notification) -> Promise Object
    			Marks the notification as read in the database
    			*/
    			console.log(notificationObject);
    			return httpHandler.request(markNotificationAsRead, {
    				notification_id: notificationObject.notification_id
    			});;
    		}
    	}
    }])
    .service('threadService', ['httpHandler', 'postThreadPHP', 'moment', 'deleteThreadComment', function(httpHandler, postThreadPHP, moment, deleteThreadComment){
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
    	this.deleteThread = function(ThreadComment) {
    		/*
    		(ThreadComment) -> Promise Object
    		*/
    		return httpHandler.request(deleteThreadComment, {
    			comment_id: ThreadComment.comment_id
    		});
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
    			//threadObject.date_posted = moment(threadObject.date_posted).fromNow();
    			console.log(threadObject);
    			return threadObject
    		}
    	};
    }]).factory('threadFactory', ['httpHandler', 'getThreadComments', 'serviceDate', 'getThreadPlayers', 'getThreadsPage', 'getThreads', 'getNumberOfUsers', 'getFormatedTabularSchedule', 'getRandomUsers', function(httpHandler, getThreadComments, serviceDate, getThreadPlayers, getThreadsPage, getThreads, getNumberOfUsers, getFormatedTabularSchedule, getRandomUsers){
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
    		},
    		numberOfUsers: function() {
    			return httpHandler.request(getNumberOfUsers, {});
    		},
    		tabularSchedule: function() {
    			return httpHandler.request(getFormatedTabularSchedule, {});
    		},
    		UsersSummary: function() {
    			/*
    			(Null) -> Promise Object
    			*/
    			return httpHandler.request(getRandomUsers, {});
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
    }).controller('controller', ['$scope', '$http', 'ngDialog', 'createConfirmedTime', 'createConfirmedTimePHP', 'loadBasicUser', '$q', 'createTentativeDate', 'markUnavailable', 'getAllBadmintonDates', 'convertMySQLToJS', 'uiCalendarConfig', 'duringHours', 'getThreads', 'moment', 'postThreadPHP', 'postCommentPHP', 'userDropdown', 'threadHelper', 'threadFactory', 'threadService', '$interval', 'notificationsFactory', 'serviceDate', 'moment', function($scope, $http, ngDialog, createConfirmedTime, createConfirmedTimePHP, loadBasicUser, $q, createTentativeDate, markUnavailable, getAllBadmintonDates, convertMySQLToJS, uiCalendarConfig, duringHours, getThreads, moment, postThreadPHP, postCommentPHP, userDropdown, threadHelper, threadFactory, threadService, $interval, notificationsFactory, serviceDate, moment) {
	$scope.data = {}, //Holding object for scopes
	$scope.data.animationsEnabled = true;
	$scope.data.thread_title = ''; //By default
	$scope.data.thread_text = '';

	$scope.data.view = 1; //By default
	notificationsFactory.CurrentUserNotifications().then(function(successResponse) {
		console.log(successResponse);
		$scope.data.notifications = successResponse.data;
		$scope.data.newNotifications = 0;
		for (var i = 0; i < $scope.data.notifications.length; i++) {
			//console.log($scope.data.notifications[i]);
			if ($scope.data.notifications[i].read_status == 0) {
				$scope.data.newNotifications++;
				$scope.data.notifications[i].style = {
					"background-color": 'rgba(41, 128, 185, 0.1)'
				};
			}
			else {
				$scope.data.notifications[i].style = '';
			}
		}
	}, function(errorResponse) {
		console.log(errorResponse);
	});

	$scope.delete = function(ThreadComment) {
		threadService.deleteThread(ThreadComment).then(function(successResponse) {
			//Remove the comment from the threads
			for (var i = 0; i < $scope.data.allThreads.length; i++) {
				for (var j = 0; j < $scope.data.allThreads[i].comments.length; j++) {
					if ($scope.data.allThreads[i].comments[j].comment_id = ThreadComment.comment_id) {
						$scope.data.allThreads[i].comments.splice(j, 1);
					}
				}
			}
		});
	}	

	$scope.propogateRead = function(notificationObject) {
		notificationsFactory.MarkAsRead(notificationObject).then(function(successResponse) {
			$window.location.href = '/' + notificationObject.a_href;
		}, function(errorResponse) {
			alert('Some error occured in handling notifications');
			console.log(errorResponse);
		});
	}

	var destroyInterval = $interval(function() {
		$scope.data.rightNow = moment().format('MMMM Do YYYY, h:mm:ss a');
	}, 1000);

	//$scope.data.doNotAsk = false; //Do not ask if you are looking to play

	$scope.data.asked = false;

	threadFactory.UsersSummary().then(function(successResponse) {
		console.log(successResponse);
		$scope.data.userSummary = successResponse.data;
	}, function(errorResponse) {
		console.log(errorResponse);
	});

	threadFactory.allThreadPlayers().then(function(successResponse) {
		$scope.data.allThreadPlayers = successResponse.data;
	}, function(errorResponse) {
		console.log(errorResponse);	
	});

	threadFactory.getThreads().then(function(successResponse) {
		var allThreads = successResponse.data;
		$scope.data.allThreads = [];
		$scope.data.UsersWhoWantToPlay = [];

        $scope.data.threadsPartitioned = {};
        var threadsPartitioned = [];
        var inputtedDates = [];

		//Now loop through all the threads and convert their MySQL Date fields to JS Date objects
        var checkDate = new Date();
        checkDate.setHours(0, 0, 0, 0);
		for (var i = 0; i < allThreads.length; i++) {
			var thread = threadHelper.threadMySQLFieldstoJS(allThreads[i]);
			for (var j = 0; j < allThreads[i].comments.length; j++) {
				allThreads[i].comments[j].date_posted = serviceDate.MySQLDatetimeToDateObject(allThreads[i].comments[j].date_posted);
				console.log(allThreads[i].comments[j].date_posted);
			}
			console.log(thread);
			if (thread.date_play > new Date()) {
				$scope.data.UsersWhoWantToPlay.push(allThreads[i].author);
			}
            if (allThreads[i].type == 1 && (allThreads[i].date_play > checkDate)) {
                var momentDate = moment(allThreads[i].date_play).format('MMM Do YY');
                var index = inputtedDates.indexOf(momentDate);
                if (index == -1) {
                    threadsPartitioned.push([]);
                    index = threadsPartitioned.length - 1;
                }
                threadsPartitioned[index].push(allThreads[i]);
            }
			$scope.data.allThreads.push(thread); 
		}
        threadsPartitioned.reverse();
        $scope.data.threadsPartitioned = threadsPartitioned;
        console.log($scope.data.threadsPartitioned);
  //       var keys = Object.keys(threadsPartitioned).length;
  //       shiftPos = keys;
  //       angular.forEach(threadsPartitioned, function(value, key) {
  //           this[shiftPos] = value;
  //           shiftPos--;
  //       }, $scope.data.threadsPartitioned);
  //       console.log($scope.data.threadsPartitioned);    
		// console.log($scope.data.allThreads);	

	}, function(errorResponse) {
		console.log(errorResponse);
	});

	threadFactory.numberOfUsers().then(function(successResponse) {
		console.log(successResponse);
		$scope.data.numberOfUsers = successResponse.data.number_of_users;
	}, function(errorResponse) {
		console.log(errorResonse);
	});

	threadFactory.tabularSchedule().then(function(successResponse) {
		var schedule = successResponse.data;
		$scope.data.tabular = [];
		for (var i =0; i < schedule.length; i++) {
			$scope.data.tabular.push(threadHelper.threadMySQLFieldstoJS(schedule[i]));
		}
	}, function(errorResponse) {
		console.log(errorResponse);
	});

	$scope.joinThread = function(thread_id) {
		actionFactory.joinThread(thread_id).then(function(successResponse) {
			console.log(successResponse);
		}, function(errorResponse) {
			console.log(errorResponse);
		});
	}

	$scope.$watch('data.thread_text', function(newValue, oldValue) {
		if ((!$scope.data.asked) && ($scope.data.thread_text.length > 2)) {
			$scope.data.asked = true;
			$scope.data.askQuestion = true;
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

	$scope.navbar = {
		discussion: 'active',
		tentative: null,
		schedule: null
	};

	$scope.handleNavbar = function(currentTab) {
		console.log('called');
		angular.forEach($scope.navbar, function(value, key) {
			console.log(key);
			console.log(currentTab);
			if (key == currentTab) {
				console.log('set');
				this[key] = 'active';
			}
			else {
				this[key] = null;
			}
		}, $scope.navbar);
		console.log($scope.navbar);
	}

    $scope.handleSmoothScroll = function(element) {
        console.log('called');
        $scope.navbar = {
            tentative: 'active',
            discussion: null,
            schedule: null
        };
    }

    $scope.postCommentPartitioned = function(thread_id, possible_comment) {
        $http({
            method: "post",
            url: postCommentPHP,
            data: {
                thread_id: thread_id,
                comment_text: possible_comment,
                parent_id: null
            },
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(successResponse) {
            console.log(successResponse);
            var newComment = successResponse.data;
            newComment.date_posted = new Date();

            for (var i = 0; i < $scope.data.allThreads.length; i++) {
                if ($scope.data.allThreads[i].thread_id == thread_id) {
                    console.log('pusshed into the non partitioned');
                    $scope.data.allThreads[i].comments.push(newComment);
                    $scope.data.allThreads[i].possible_comment = '';
                }
            }

            //Now push it into the thread parititoned
            // for (var i = 0; i < $scope.data.threadsPartitioned.length; i++) {
            //     for (var j = 0; j < $scope.data.threadsPartitioned[i].length; j++) {
            //         if ($scope.data.threadsPartitioned[i][j].thread_id == thread_id) {
            //             $scope.data.threadsPartitioned[i][j].comments.push(newComment);
            //         }
            //     }
            // }

            console.log($scope.data.allThreads);
            console.log($scope.data.threadsPartitioned);
            //Clear the comment box
        }, function(errorResponse) {
            console.log(errorResponse);
        });   
    }

 	$scope.postComment = function(thread_id) {
		var loopBool = {
			looping: true
		};

		console.log('logged');

		for (var i = 0; i < $scope.data.allThreads.length; i++) {
			if ($scope.data.allThreads[i].thread_id == thread_id) {
				var indexOfThread = i;
				var threadID = $scope.data.allThreads[i].thread_id;
				var comment = $scope.data.allThreads[i].possible_comment;
			}
		}

		//Sent the post request to post the commment
		$http({
			method: "post",
			url: postCommentPHP,
			data: {
				thread_id: threadID,
				comment_text: comment,
				parent_id: null
			},
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
		}).then(function(successResponse) {
			console.log(successResponse);
			var newComment = successResponse.data;
			newComment.date_posted = new Date();
			for (var i = 0; i < $scope.data.allThreads.length; i++) {
				if ($scope.data.allThreads[i].thread_id == threadID) {
					$scope.data.allThreads[i].comments.push(newComment);
					$scope.data.allThreads[i].possible_comment = '';
				}
			}
			//Clear the comment box
		}, function(errorResponse) {
			console.log(errorResponse);
		});

		// function sendRequest (iteration, value, thread_id) {
		// 	var deferred = $q.defer();

		// 	if (value.thread_id == thread_id) {
		// 		$http({
		// 			method: "post",
		// 			url: postCommentPHP,
		// 			data: {
		// 				thread_id: value.thread_id,
		// 				comment_text: value.possible_comment,
		// 				parent_id: null
		// 			},
		// 			headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
		// 		}).then(function(successResponse) {
		// 			deferred.resolve();
		// 			console.log(successResponse);
		// 		}, function(errorResponse) {
		// 			deferred.reject('With reason...');
		// 			console.log(errorResponse);
		// 		});
		// 	}
		// 	else {
		// 		deferred.reject();
		// 	}
		// 	return deferred.promise;
		// }

		// angular.forEach($scope.data.threads, function(value, key) {
		// 	if (this.looping) {
		// 		for (var i = 0; i < value.length; i++) {
		// 			if (this.looping) {
		// 				sendRequest(i, value[i], thread_id).then(function(successResponse) {
		// 					this.looping = false;
		// 				}, function(errorResponse) {
		// 					//Do nothing
		// 				});
		// 				if (value[i].thread_id == thread_id) {
		// 					value[i].possible_comment = '';
		// 				}
		// 			}
		// 		}
		// 	}
		// }, loopBool);
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

            var newThread = successResponse.data;
            newThread.date_posted = new Date();
            newThread.author = $scope.user;

            //Push into the array of threads
            $scope.data.allThreads.unshift(newThread);
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
}]);
