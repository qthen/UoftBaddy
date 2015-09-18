var app = angular./**
* app Module
*
* Description
*/
module('app', ['ui.bootstrap', 'ui.calendar', 'ngDialog', 'angularMoment', 'angularSmoothscroll', 'ngScrollSpy'])
    .service('serviceDate', function() {
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
    }])
    .factory('dateFactory', ['moment', 'serviceDate', function(moment, serviceDate) {
        var factoryDate = {};

        factoryDate.MySQLDatetimeToDateObject = function(MySQLDatetimeString) {
            return serviceDate.MySQLDatetimeString(MySQLDatetimeString);
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
    }])
    .factory('httpFactory', ['$http', function($http){
        return function name(){
            
        };
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
    }]).config(function($provide) {
        $provide.constant('createConfirmedTime', 'html/ngDialog/create_date.html');
        $provide.constant('createConfirmedTimePHP', 'postRequests/user/createBadmintonDate.php');
        $provide.constant('loadBasicUser', 'postRequests/profile/loadUser.php');
        $provide.constant('createTentativeDate', 'html/ngDialog/create_tentative_date.html');
        $provide.constant('markUnavailable', 'html/ngDialog/mark_unavailable_date.html');

        $provide.constant('getUserEvents', 'postRequests/user/getUserEvents.php');
        $provide.constant('getCreatedEvents', 'postRequests/user/getCreatedUserEvents.php');

        $provide.constant('getUserDropdown', 'postRequests/user/getUserDropdown.php');

         $provide.value('MySQLtoJS', function(datetimeString) {
            var t = datetimeString.split(/[- :]/);
            var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
            return d;
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
    });

function rdWidget(){
    var d={
        transclude:!0,
        template:'<div class="widget" ng-transclude></div>',
        restrict:"EA"
    };
    return d
}
angular.module("app").directive("rdWidget",rdWidget);

function rdLoading(){
    var d=
    {
        restrict:"AE",
        template:'<div class="loading"><div class="double-bounce1"></div><div class="double-bounce2"></div></div>'
    };
    return d
}
angular.module("app").directive("rdLoading",rdLoading);

function rdWidgetBody(){
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
}
angular.module("app").directive("rdWidgetBody",rdWidgetBody);

function rdWidgetFooter(){
    var e=
    {
        requires:"^rdWidget",
        transclude:!0,
        template:'<div class="widget-footer" ng-transclude></div>',
        restrict:"E"
    };
return e}
angular.module("app").directive("rdWidgetFooter",rdWidgetFooter);

function rdWidgetTitle(){
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
}
angular.module("app").directive("rdWidgetHeader",rdWidgetTitle);


app.controller('controller', ['$scope', '$http', 'ngDialog', 'createConfirmedTime', 'createConfirmedTimePHP', 'loadBasicUser', '$q', 'moment', 'getUserEvents', 'getCreatedEvents', 'dateFactory', 'userDropdown', function($scope, $http, ngDialog, createConfirmedTime, createConfirmedTimePHP, loadBasicUser, $q, moment, getUserEvents, getCreatedEvents, dateFactory, userDropdown) {
    $scope.data = {}, //Holding object for scopes
    $scope.data.animationsEnabled = true;
    $scope.data.badmintonDates = [
    ];

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

    $scope.updateView = function(newView) {
        switch (newView) {
            case 'all':
                $scope.showing = $scope.data.yrevents.length;
                break;
            case 'upcoming':
                $scope.showing = $scope.data.upcomingEvents.length;
                break;
            case 'past':
                $scope.showing = $scope.data.pastEvents.length;
                break;
            case 'you':
                $scope.showing = $scope.data.hostedByYou.length;
                break;
        }
    }


    $scope.init = function(userID, defaultTab) {
        $scope.data = {};

        $scope.tab = {};
        $scope.tab[defaultTab] = true;

        $scope.user = {
            user_id: userID
        };
        console.log($scope.user);

        if ($scope.user.user_id) {
            //Load the basic user fields
            var promiseLoadUser = $http({
                method: "post",
                url: loadBasicUser,
                data: {
                    profile_id: $scope.user.user_id
                },
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).then(function(successResponse) {
                $scope.user = successResponse.data;
            }, function(errorResponse) {
                console.log(errorResponse);
            })
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
                $scope.outOf = $scope.data.yrevents.length;

                $scope.data.hostedByYou = [];
                $scope.data.hostedByOthers = [];
                $scope.data.pastEvents = [];
                $scope.data.upcomingEvents = [];

                $scope.data.yrevents = dateFactory.handleDateArray($scope.data.yrevents); //Convert to the correct date format

                console.log($scope.data.yrevents);
                //Now figure out where the current user is a creator
                for (var i = 0; i < $scope.data.yrevents.length; i++) {
                    if ($scope.data.yrevents[i].creator.user_id == $scope.user.user_id) {
                        $scope.data.yrevents[i].created = true;
                        $scope.data.hostedByYou.push($scope.data.yrevents[i]);
                    }
                    else {
                        $scope.data.yrevents[i].created = false;
                        $scope.data.hostedByOthers.push($scope.data.yrevents[i]);
                    }

                    //Check if the current event is already past
                    if ($scope.data.yrevents[i].begin_datetime < new Date()) {
                        //Date has already passed
                        $scope.data.pastEvents.push($scope.data.yrevents[i]);
                    }
                    else {
                        //Date is upcomfing
                        $scope.data.upcomingEvents.push($scope.data.yrevents[i]);
                    }
                }
                $scope.data.filteredEvents = $scope.data.yrevents;

                $scope.updateView(defaultTab);
                console.log($scope.data.upcomingEvents);
            }, function(errorResponse) {
                alert('Error fetching your events');
            });
        }
        else {
            alert('Not logged in');
        }
    }

    $scope.updateView = function(view) {
        switch (view) {
            case 'all':
                $scope.data.filteredEvents = $scope.data.yrevents;
                break;
            case 'upcoming':
                $scope.data.filteredEvents = $scope.data.upcomingEvents;
                break;
            case 'past':
                $scope.data.filteredEvents = $scope.data.pastEvents;
                break;
            case 'you':
                $scope.data.filteredEvents = $scope.dat.hostedByYou;
                break
        }
    }
}]);
