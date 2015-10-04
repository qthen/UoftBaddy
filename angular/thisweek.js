angular./**
* app Module
*
* Description
*/
module('app', ['mwl.calendar', 'ui.bootstrap', 'angularMoment'])
	.config(['$provide', 'moment', function($provide, moment) {
		$provide.constant('getAthleticCenter', 'postRequests/index/getAthelticCentreSchedule.php');
        $provide.constant('getUserNotifications', 'postRequests/user/getUserNotifications.php');
        $provide.constant('getUserDropdown', 'postRequests/user/getUserDropdown.php');
        $provide.constant('markNotificationAsRead', 'postRequests/user/MarkNotificationAsRead.php');
        moment.locale('en', {
          week : {
            dow : 1 // Monday is the first day of the week
          }
        });
	}]).service('httpHandler', ['$http', '$q', function($http, $q){
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
    }]).service('serviceDate', function() {
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
    }]).factory('weekFactory', ['httpHandler', 'getAthleticCenter', function(httpHandler, getAthleticCenter){
		return {
			AthleticCenterSchedule: function() {
				/*
				(Null) -> Promise Object
				*/
				return httpHandler.request(getAthleticCenter, {});
			}
		}
	}]).service('serviceDate', function() {
        this.MySQLDatetimeToDateObject = function(MySQLDatetimeString) {
            var t = MySQLDatetimeString.split(/[- :]/);
            return new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
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
    }).factory('dateFactory', ['moment', 'serviceDate', function(moment, serviceDate) {
        /*
        Primary factory for manipulating date arrays and array of badminton dates
         */
        var factoryDate = {};

        factoryDate.handleDateArray = function(arrayInput) {

        }

        factoryDate.MySQLDatetimeToDateObject = function(MySQLDatetimeString) {
            return serviceDate.MySQLDatetimeString(MySQLDatetimeString);
        }

        factoryDate.arrayToCalendar = function(arrayInput) {
            /*
            (Array of Badminton Dates) -> Event Array
            Handles the array passed into it and converts it into an array processable by mwl-calendar
             */
            if (Array.isArray(arrayInput)) {
                var returnArray = [];
                //console.log(arrayInput);
                for (var i = 0; i < arrayInput.length; i++) {
                    //Create the MySQL datetime strings into JS Date objects
                    var date_id = arrayInput[i].date_id;
                    var startsAt = serviceDate.MySQLDatetimeToDateObject(arrayInput[i].begin_datetime);
                    var endsAt = serviceDate.MySQLDatetimeToDateObject(arrayInput[i].end_datetime);
                    // var endsAt = serviceDate.MySQLDatetimeToDateObject(arrayInput[i].begin_datetime);
                    // endsAt.setHours(startsAt.getHours() + 1);
                    var title = arrayInput[i].datename;
                    var type = 'important';

/*                    if (arrayInput[i].begin_datetime >= new Date()) {
                        arrayInput[i].message = 'Begins at ' + moment(arrayInput[i].begin_datetime).format('MMMM Do YYYY, h:mm a') + ' and likely ends at ' + moment(arrayInput[i].end_datetime).format('MMMM Do YYYY, h:mm a');
                    }
                    else {
                        arrayInput[i].message = 'Began at ' + moment(arrayInput[i].begin_datetime).format('MMMM Do YYYY, h:mm a') + ' and likely ended at ' + moment(arrayInput[i].end_datetime).format('MMMM Do YYYY, h:mm a');
                    }*/
                    var object = {
                        startsAt: startsAt,
                        endsAt: endsAt,
                        title: title,
                        type: type,
                        date_id: date_id
                    }
                    console.log(object);
                    //Do a smart check on whether or not the event is almost full
                    returnArray.push(object);
                }
                return returnArray;
            }
        }

        factoryDate.handleDateArray = function(arrayInput) {
            /*
            (Array) -> Array
            This factory method handles all the conversion to the moment and Date object for an array of badminton dates
             */
            if (Array.isArray(arrayInput)) {
                //console.log(arrayInput);
                for (var i = 0; i < arrayInput.length; i++) {
                    //Create the MySQL datetime strings into JS Date objects
                    arrayInput[i].begin_datetime = serviceDate.MySQLDatetimeToDateObject(arrayInput[i].begin_datetime),
                    arrayInput[i].end_datetime = serviceDate.MySQLDatetimeToDateObject(arrayInput[i].end_datetime);

/*                    var t = arrayInput[i].begin_datetime.split(/[- :]/);
                    var start = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
                    arrayInput[i].begin_datetime = start;
                    var t = arrayInput[i].end_datetime.split(/[- :]/);
                    var end = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
                    arrayInput[i].end_datetime = end;
*/

                    //Also create appropiate message to display to the user
                    if (arrayInput[i].begin_datetime >= new Date()) {
                        arrayInput[i].message = 'Begins at ' + moment(arrayInput[i].begin_datetime).format('MMMM Do YYYY, h:mm a') + ' and likely ends at ' + moment(arrayInput[i].end_datetime).format('MMMM Do YYYY, h:mm a');
                    }
                    else {
                        arrayInput[i].message = 'Began at ' + moment(arrayInput[i].begin_datetime).format('MMMM Do YYYY, h:mm a') + ' and likely ended at ' + moment(arrayInput[i].end_datetime).format('MMMM Do YYYY, h:mm a');
                    }
                }
                return arrayInput;
            }
        }
        return factoryDate;
    }]).controller('controller', ['$scope', '$http', 'weekFactory', 'dateFactory', 'notificationsFactory', 'userDropdown', function($scope, $http, weekFactory, dateFactory, notificationsFactory, userDropdown){
		$scope.data = {};

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

        $scope.toggleSidebar = function() {
            /*
            Had to simplify a lot of routing
             */
            $scope.toggle = !$scope.toggle;
        }

        $scope.toggle = true;

        //For the mwl.calendar
        $scope.calendarView = 'week';
        var calendarDay = new Date();
        if (calendarDay.getDate() == 0) {
            calendarDay.setDate(calendarDay.getDate() - 1);
        }
        $scope.calendarDay = calendarDay;
        //Get the start date of the week
        function getMonday( date ) {
            var day = date.getDay() || 7;  
            if( day !== 1 ) 
                date.setHours(-24 * (day - 1)); 
            return date;
        }
        var mondayOfWeek = getMonday($scope.calendarDay);
        $scope.mondayOfWeek = mondayOfWeek;
        console.log(mondayOfWeek);
        var sundayOfWeek = new Date();
        sundayOfWeek.setDate(mondayOfWeek.getDate() + 6);
        $scope.sundayOfWeek = sundayOfWeek;

        var nextSchedule = new Date();
        nextSchedule.setDate(sundayOfWeek.getDate() + 1);
        $scope.nextSchedule = nextSchedule;

        //var tomorrowDate = new Date();
        // tomorrowDate.setDate(tomorrowDate.getDate() + 1);
        // $scope.tomorrowDay = tomorrowDate;

		weekFactory.AthleticCenterSchedule().then(function(successResponse) {
            console.log(successResponse);
            $scope.data.schedule = dateFactory.arrayToCalendar(successResponse.data);
            console.log($scope.data.schedule);
		}, function(errorResponse) {
			console.log(errorResponse);	
		});
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
		
	}])