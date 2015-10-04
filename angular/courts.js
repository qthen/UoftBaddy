var app = angular./**
* app Module
*
* Description
*/
module('app', ['mwl.calendar', 'ui.bootstrap', 'ngDialog', 'angularMoment']).
    config(function($provide) {
        $provide.constant('createConfirmedTime', 'html/ngDialog/create_date.html');
        $provide.constant('createConfirmedTimePHP', 'postRequests/user/createBadmintonDate.php');
        $provide.constant('loadBasicUser', 'postRequests/profile/loadUser.php');
        $provide.constant('createTentativeDate', 'html/ngDialog/create_tentative_date.html');
        $provide.constant('markUnavailable', 'html/ngDialog/mark_unavailable_date.html');
        $provide.constant('getAllBadmintonDates', 'postRequests/index/getTodayTomorrowCourts.php');
        $provide.constant('loadTopBar', 'postRequests/index/getTopBar.php');
        $provide.constant('getUserDropdown', 'postRequests/user/getUserDropdown.php');

        $provide.constant('getUsersPlaying', 'postRequests/index/getUsersPlaying.php');
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
    }]).factory('courtsFactory', ['httpHandler', 'getAllBadmintonDates', 'getUsersPlaying', function(httpHandler, getAllBadmintonDates, getUsersPlaying){
        return {
            courts: function() {
                /*
                (Null) -> Promise Objects
                Gets the associated promise object with the courts for today and tomorrow
                */
                return httpHandler.request(getAllBadmintonDates, {
                });
            },
            usersPlaying: function() {
                /*
                (Null) -> Promise Object
                */
                return httpHandler.request(getUsersPlaying, {});
            }
        }
    }]).factory('dateFactory', ['moment', 'serviceDate', function(moment, serviceDate) {
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
                var returnArray = {
                    todayEvents: [],
                    tomorrowEvents: []
                };

                //console.log(arrayInput);
                for (var i = 0; i < arrayInput.length; i++) {
                    //Create the MySQL datetime strings into JS Date objects
                    var date_id = arrayInput[i].date_id;
                    var startsAt = serviceDate.MySQLDatetimeToDateObject(arrayInput[i].begin_datetime);
                    var endsAt = serviceDate.MySQLDatetimeToDateObject(arrayInput[i].end_datetime);
                    var title = arrayInput[i].datename;
                    var type = 'info';

                    console.log(arrayInput[i]);

                    if (arrayInput[i].joined) {
                        if (arrayInput[i].absent) {
                            title = '<strong>[Absent]</strong> ' + title + '<i class="fa fa-circle" style="color:red;position:absolute;right:1;bottom:1;"></i>';
                        }
                        else {
                            title = '<strong>[Joined]</strong> ' + title + '<i class="fa fa-circle" style="color:green;position:absolute;right:1;bottom:1;"></i>';
                        }
                    }

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
                    //Do a smart check on whether or not the event is almost full
                    if (arrayInput[i].attendees)
                    if (arrayInput[i].full) {
                        object.type = 'danger';
                    }
                    else {
                        var freeSpace = parseInt(arrayInput[i].max_attendants) - parseInt(arrayInput[i].attendees);
                        if (freeSpace <= 2) {
                            object.type = 'warning';
                        }
                        else {
                            object.type = 'info';
                        }
                    }
                    if (object.startsAt.getDay() == new Date().getDay()) {
                        //Today
                        returnArray.todayEvents.push(object);                        
                    }
                    else {
                        returnArray.tomorrowEvents.push(object);
                    }
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
    }).controller('controller', ['$scope', '$http', 'ngDialog', 'createConfirmedTime', 'createConfirmedTimePHP', 'loadBasicUser', '$q', 'createTentativeDate', 'markUnavailable', 'getAllBadmintonDates', 'convertMySQLToJS', 'duringHours', 'loadTopBar', 'dateFactory', '$window', 'courtsFactory', 'userDropdown', 'serviceDate', 'notificationsFactory', function($scope, $http, ngDialog, createConfirmedTime, createConfirmedTimePHP, loadBasicUser, $q, createTentativeDate, markUnavailable, getAllBadmintonDates, convertMySQLToJS, duringHours, loadTopBar, dateFactory, $window, courtsFactory, userDropdown, serviceDate, notificationsFactory) {
        //$scope.data = {};
        $scope.data = {};
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

        $scope.getUserDropdown = function() {
            userDropdown.dropdownFields().then(function(successResponse) {
                console.log(successResponse);
                $scope.data.dropdown = {
                    user: successResponse.data.user,
                    fields: successResponse.data.fields
                };
                console.log($scope.data.dropdown);
            }, function(errorResponse) {
                console.log(errorResponse);
            });
        }
        $scope.getUserDropdown();

        console.log($scope.data);
        //For the mwl.calendar
        $scope.calendarView = 'day';
        $scope.calendarDay = new Date();
        var tomorrowDate = new Date();
        tomorrowDate.setDate(tomorrowDate.getDate() + 1);
        $scope.tomorrowDay = tomorrowDate;

/*        $scope.events = [
          {
            title: 'My event title', // The title of the event
            type: 'info', // The type of the event (determines its color). Can be important, warning, info, inverse, success or special
            startsAt: new Date(2013,5,1,1), // A javascript date object for when the event starts
            endsAt: new Date(2014,8,26,15), // Optional - a javascript date object for when the event ends
            editable: false, // If edit-event-html is set and this field is explicitly set to false then dont make it editable.
            deletable: false, // If delete-event-html is set and this field is explicitly set to false then dont make it deleteable
            draggable: true, //Allow an event to be dragged and dropped
            resizable: true, //Allow an event to be resizable
            incrementsBadgeTotal: true, //If set to false then will not count towards the badge total amount on the month and year view
            recursOn: 'year', // If set the event will recur on the given period. Valid values are year or month
            cssClass: 'a-css-class-name' //A CSS class (or more, just separate with spaces) that will be added to the event when it is displayed on each view. Useful for marking an event as selected / active etc
          }
        ];*/

        $scope.toggle = true;

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

        $scope.navbar = {
            crowd: 'active',
            info: null,
            stats: null
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

        $scope.dropdown = {
            chosen: 'Split'
        };

        $scope.handleDropdown = function(dropdownChoice) {
            console.log('logged');
            $scope.dropdown.chosen = dropdownChoice;
            console.log($scope.dropdown);   
        }

        function scopeUserInCourt(court) {
            for (var i = 0; i < court.attendees.length; i++) {
                if (court.attendees[i].user_id == $scope.user.user_id) {
                    return true;
                }
            }
            return false;
        }

        //Get the badminton courts
        courtsFactory.courts().then(function(successResponse) {
            console.log(successResponse);
            var courts = successResponse.data;
            var courts = dateFactory.arrayToCalendar(courts);
            $scope.todayEvents = courts.todayEvents;
            $scope.tomorrowEvents = courts.tomorrowEvents;
            console.log($scope.todayEvents);
            console.log($scope.tomorrowEvents);

            //Now figure out which courts have spice
            $scope.data.todayFreeCourts = [];
            $scope.data.tomorrowFreeCourts = [];

            var courts = successResponse.data;

            for (var i = 0; i < courts.length; i++) {
                courts[i].begin_datetime = serviceDate.MySQLDatetimeToDateObject(courts[i].begin_datetime);
                if (courts[i].begin_datetime.getDay() == new Date().getDay()) {
                    $scope.data.todayFreeCourts.push(courts[i]);
                }
                else {
                    $scope.data.tomorrowFreeCourts.promiseGetUserh(courts[i]);
                }
            }
            console.log($scope.data.todayFreeCourts);
        });

        courtsFactory.usersPlaying().then(function(successResponse) {
            $scope.data.usersPlaying = successResponse.data;
        }, function(errorResponse) {
            console.log(errorResponse);
        });


        $scope.init = function(userID) {
            $scope.userPromise = $q.defer();

            $scope.user = {
                'user_id': userID
            }
            console.log($scope.user);

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
                    console.log(errorResponse);
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

        $scope.createDateDialog = function(option) {
            if (option == 'today') {
                $scope.createdDay = $scope.calendarDay;
            }
            else {
                $scope.createdDay = $scope.tomorrowDay;
            }
            $scope.userPromise.promise.then(function(successResponse) {
                if ($scope.user.user_id) {
                ngDialog.open({
                        template: createConfirmedTime,
                        scope: $scope,
                        controller: ['$scope', '$http', 'ngDialog', 'createConfirmedTimePHP', 'duringHours', 'moment', '$window', function($scope, $http, ngDialog, createConfirmedTimePHP, duringHours, moment, $window){

                            $scope.data = {
                                currentDay: $scope.createdDay
                            }; //Scope holding object for primitive scope

                            //Defaults for form code
                            $scope.data.eventName = 'Badminton Date',
                            $scope.data.dt = $scope.data.currentDay,
                            $scope.data.mytime = new Date();
                            $scope.data.endtime = new Date();

                            console.log($scope.data.dt);

                            //Watcher on the date value to customize the title of the date

                            $scope.$watch('data.dt', function(newValue, oldValue) {
                                console.log(newValue);
                                if (Object.prototype.toString.call(newValue) === "[object Date]") {
                                    console.log('hi');
                                    //if (Date.parse($scope.data.thread_title) || (!$scope.data.thread_title) || ($scope.data.thread_title.length < 3)) {
                                        $scope.data.eventName = 'Badminton - ' + moment(newValue).format('MMMM Do YYYY');
                                    //}
                                }
                            });

                            //Code to submit the form
                            $scope.submit = function() {

                                if (duringHours($scope.data.mytime) && duringHours($scope.data.endtime)) {
                                    var dateEventMySQL = moment($scope.data.dt).format('YYYY-MM-DD');

                                    console.log(dateEventMySQL);
                                    console.log($scope.data.dt);
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
                                            datename: $scope.data.eventName,
                                            summary: $scope.data.summary,
                                            max_attendants : $scope.data.maxAttendants
                                        },
                                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                                    });

                                    promise.then(function(successResponse) {
                                        var dialg = ngDialog.open({
                                            template: 'html/ngDialog/success_create_confirm_date.html'
                                        });
                                        dialog.closePromise.then(function() {
                                            $window.location.reload();
                                        })
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

        $scope.eventClicked = function(calendarObject) {
            $window.location.href = '/date.php?id=' + calendarObject.date_id;
        }
    }]);
