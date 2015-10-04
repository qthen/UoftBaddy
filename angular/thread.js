var app = angular./**
* app Module
*
* Description
*/
module('app', ['ui.bootstrap', 'ui.calendar', 'ngDialog', 'angularMoment'])
.config(function($provide) {
        $provide.constant('createConfirmedTime', 'html/ngDialog/create_date.html');
        $provide.constant('createConfirmedTimePHP', 'postRequests/user/createBadmintonDate.php');
        $provide.constant('loadBasicUser', 'postRequests/profile/loadUser.php');
        $provide.constant('createTentativeDate', 'html/ngDialog/create_tentative_date.html');
        $provide.constant('markUnavailable', 'html/ngDialog/mark_unavailable_date.html');
        $provide.constant('getAllBadmintonDates', 'postRequests/index/getAllBadmintonDates.php');
        $provide.constant('getThreads', 'postRequests/index/getAllThreads.php');
        $provide.constant('postThreadPHP', 'postRequests/user/postThread.php');
        $provide.constant('postCommentPHP', 'postRequests/user/postThreadComment.php');
        $provide.constant('getThread', 'postRequests/thread/getThread.php');
        $provide.constant('loadCurrentUser', 'postRequests/user/loadCurrentUser.php');

        $provide.constant('getThreadComments', 'postRequests/thread/getThreadComments.php');

        $provide.constant('getUserDropdown', 'postRequests/user/getUserDropdown.php');
        $provide.constant('getThreadParticipants', 'postRequests/thread/getThreadParticipants.php');

        //For joining threads
        $provide.constant('joinThread', 'postRequests/user/joinThread.php');
        $provide.constant('conditionallyJoinThread', 'postRequests/user/conditionalJoinThread.php');
        $provide.constant('getUserNotifications', 'postRequests/user/getUserNotifications.php');
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
    })  .service('currentUser', ['httpHandler', 'loadCurrentUser', function(httpHandler, loadCurrentUser){
        this.promise = function() {
            return httpHandler.request(loadCurrentUser, {});
        }
    }]).service('dateHelper', function(){
        this.JSDateObjectToMySQLTimeTrimmed = function(dateObject, timeObject) {
            /*
            (Date, Date) -> String
            Takes the first input (as the date) and the second input (as the time) and merges them together and returns a properly formatted MySQL Datetime string for insertion into the database
            */
            var dateEventMySQL = dateObject.toISOString().substring(0, 10)
            var timeEventMySQL = timeObject.getHours();

            return dateEventMySQL + ' ' + timeEventMySQL + ':00:00';
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
                return threadObject
            }
        };
    }]).factory('threadFactory', ['httpHandler', 'getThread', 'getThreadComments', 'getThreadParticipants', function(httpHandler, getThread, getThreadComments, getThreadParticipants){
        return {
            thread: function(thread_id) {
                /*
                (Int) -> Promise Object
                Returns the promise object from the http request to fetch the thread
                */
                return httpHandler.request(getThread, {
                    thread_id: thread_id
                });
            },
            // threadComments: function(thread_id) {
            //     /*
            //     (Int) -> Array of Comments
            //     */
            //     httpHandler.request(getThreadComments, {
            //         thread_id: thread_id
            //     }).then(function(successResponse) {
            //         return successResponse.data;
            //     }, function(errorResponse) {
            //         console.log(errorResponse);
            //         return [];
            //     });
            // },
            threadParticipants: function(thread_id) {
                /*
                (Int) -> Promise
                */
                return httpHandler.request(getThreadParticipants, {
                    thread_id: thread_id
                });
            }
        }
    }])
    .factory('threadActionFactory', ['httpHandler', 'joinThread', 'conditionallyJoinThread', 'postCommentPHP', function(httpHandler, joinThread, conditionalJoinThread, postCommentPHP){
        return {
            joinThread: function(thread_id) {
                /*
                (Int) -> Promise Object
                */
                return httpHandler.request(joinThread, {
                    thread_id: thread_id
                });
            },
            conditionallyJoinThread: function(thread_id) {
                /*
                (Int) -> Promise Object
                */
                return httpHandler.request(conditionallyJoinThread, {
                    thread_id: thread_id
                });
            },
            postCommentOnThread: function(threadObject, commentText) {
                /*
                (ThreadObject, String) -> Promise Object
                Posts the comment into the database and returns the assoicated promise object to it
                */
                return httpHandler.request(postCommentPHP, {
                    thread_id: thread_id,
                    comment_text: commentText
                })
            }
        }
    }]).directive('enterSubmit', function () {
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
    }).controller('controller', ['$scope', '$http', 'ngDialog', 'createConfirmedTime', 'createConfirmedTimePHP', 'loadBasicUser', '$q', 'createTentativeDate', 'markUnavailable', 'getAllBadmintonDates', 'convertMySQLToJS', 'uiCalendarConfig', 'duringHours', 'getThreads', 'moment', 'postThreadPHP', 'postCommentPHP', 'getThread', 'threadActionFactory', 'dateHelper', 'threadFactory', 'threadHelper', 'userDropdown', 'currentUser', 'notificationsFactory', 'serviceDate', function($scope, $http, ngDialog, createConfirmedTime, createConfirmedTimePHP, loadBasicUser, $q, createTentativeDate, markUnavailable, getAllBadmintonDates, convertMySQLToJS, uiCalendarConfig, duringHours, getThreads, moment, postThreadPHP, postCommentPHP, getThread, threadActionFactory, dateHelper, threadFactory, threadHelper, userDropdown, currentUser, notificationsFactory, serviceDate) {
    $scope.data = {}, //Holding object for scopes
    $scope.data.animationsEnabled = true;
    $scope.data.badmintonDates = [
    ];

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

    $scope.propogateRead = function(notificationObject) {
        notificationsFactory.MarkAsRead(notificationObject).then(function(successResponse) {
            $window.location.href = '/' + notificationObject.a_href;
        }, function(errorResponse) {
            alert('Some error occured in handling notifications');
            console.log(errorResponse);
        });
    }

    //Loading the current user
    currentUser.promise().then(function(successResponse) {
        $scope.user = successResponse.data;
    }, function(errorResponse) {
        console.log(errorResponse);
    });

    $scope.getUserDropdown = function() {
        userDropdown.dropdownFields().then(function(successResponse) {
            console.log(successResponse);
            $scope.data.dropdown = {
                user: successResponse.data.user,
                fields: successResponse.data.fields
            };
            console.log($scope.data.dropdown);
        }, function(errorResponse) {
            console.log(errorMessage);
        });
    }
    $scope.getUserDropdown();

    $scope.joinThread = function(thread_id) {
        actionFactory.joinThread(thread_id).then(function(successResponse) {
            console.log(successResponse);
        }, function(errorResponse) {
            console.log(errorResponse);
        });
    }

    $scope.conditionallyJoinThread = function(thread_id) {
        /*
        Expects the data.begin_conditional and data.end_conditional to be filled out
        */
        if ($scope.data.begin_conditional && $scope.data.end_conditional) {

        }
    }

    $scope.createConditionals = function(thread_id) {

    }

    $scope.init = function(thread_id) {
        threadFactory.thread(thread_id).then(function(successResponse) {
            var thread = successResponse.data;

            console.log(successResponse);

            //Convert the thread to the proper formats
            $scope.thread = threadHelper.threadMySQLFieldstoJS(thread);

            for (var i = 0; i < $scope.thread.comments.length; i++) {
                $scope.thread.comments[i].date_posted = serviceDate.MySQLDatetimeToDateObject($scope.thread.comments[i].date_posted);
            }

            //Get the commments
            //$scope.thread.comments = threadFactory.threadComments($scope.thread.thread_id);

            threadFactory.threadParticipants($scope.thread.thread_id).then(function(successResponse) {
                $scope.threadParticipants = successResponse.data;
            }, function(errorResponse) {
                console.log(errorResponse);
            });

            console.log($scope.thread);

            //console.log($scope.threadParticipants);
            console.log(successResponse);
        }, function(errorResponse) {
            console.log(errorResponse);
        });


    }

    $scope.postComment = function() {
        var loopBool = {
            looping: true
        };

        console.log('logged')
        //Sent the post request to post the commment
        $http({
            method: "post",
            url: postCommentPHP,
            data: {
                thread_id: $scope.thread.thread_id,
                comment_text: $scope.data.possibleComment,
                parent_id: null
            },
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(successResponse) {
            console.log(successResponse);
            $scope.possibleComment = '';
            var newComment = successResponse.data;
            newComment.date_posted = new Date();
            $scope.thread.comments.push(newComment);
            //Clear the comment box
            $scope.data.possibleComment = '';
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
}]);
