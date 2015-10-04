angular./**
* app Module
*
* Description
*/
module('app', ['ui.bootstrap', 'ui.calendar', 'ngDialog', 'ngSanitize', 'angularMoment'])
    .config(function($provide) {
        $provide.constant('createConfirmedTime', 'html/ngDialog/create_date.html');
        $provide.constant('createConfirmedTimePHP', 'postRequests/user/createBadmintonDate.php');
        $provide.constant('loadBasicUser', 'postRequests/profile/loadUserProfile.php');
        $provide.constant('createTentativeDate', 'html/ngDialog/create_tentative_date.html');
        $provide.constant('markUnavailable', 'html/ngDialog/mark_unavailable_date.html');
        $provide.constant('loadUser', 'postRequests/profile/loadUser.php');
        $provide.constant('getUserActions', 'postRequests/user/getUserActions.php');
        $provide.constant('getUserEvents', 'postRequests/user/getUserEvents.php');
        $provide.constant('getCreatedEvents', 'postRequests/user/getCreatedUserEvents.php');
        $provide.constant('getUserDropdown', 'postRequests/user/getUserDropdown.php');
        $provide.constant('getUserNotifications', 'postRequests/user/getAllUserNotifications.php');
        $provide.constant('markAllAsRead', 'postRequests/user/MarkAllAsRead.php');

        $provide.value('MySQLtoJS', function(datetimeString) {
            var t = datetimeString.split(/[- :]/);
            var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
            return d;
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
            (String, JSON) -> Promise Object`rofi
             */
            var deferred = $q.defer();

            if (url && angular.isObject(data) && !Array.isArray(data)) {
                deferred.resolve();

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
    }]).factory('notificationsFactory', ['httpHandler', 'getUserNotifications', 'markAllAsRead', function(httpHandler, getUserNotifications, markAllAsRead) {
        return {
            CurrentUserNotifications: function() {
                return httpHandler.request(getUserNotifications, {});
            },
            ReadAll: function() {
                /*
                (Null) -> Promise
                Reads all the notifications and returns the associated promise object
                */
                return httpHandler.request(markAllAsRead, {});
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
    }])
    .service('actionHelper', ['moment', function(moment){
        this.formatActionMessage = function(actionObject) {
            /*
            (actionObject) -> ActionObject
            Formats the action object for display on the site
            */
            switch (actionObject.class) {
                case 'JoinSite':
                    actionObject.action_message = 'Joined the site on ' + moment(actionObject.date_action).format('MMMM Do YYYY, h:mm a');
                    break;
                case 'PostedThread':
                    actionObject.action_message = 'Posted on ' + moment(actionObject.date_action).format('MMMM Do YYYY, h:mm a');
                    break;
                case 'PostedCommentOnThread':
                    actionObject.action_message = 'Comment on ' + moment(actionObject.date_action).format('MMMM Do YYYY, h:mm a');
                    break;
                case 'ProposeBadmintonDate':
                    actionObject.action_message = 'Created on ' + moment(actionObject.date_action).format('MMMM Do YYYY, h:mm a');
                    break;
                case 'LeaveBadmintonDate':
                    actionObject.action_message = 'Left on ' + moment(actionObject.date_action).format('MMMM Do YYYY, h:mm a');
                    break;
                case 'JoinBadmintonDate':
                    actionObject.action_message = 'Joined event on ' + moment(actionObject.date_action).format('MMMM Do YYYY, h:mm a');
                    break;
            }
            return actionObject;
        }
        
    }])
    .factory('profileFactory', ['$http', 'httpHandler', 'loadBasicUser', 'getUserActions', function($http, httpHandler, loadBasicUser, getUserActions){
        return {
            barebonesProfile: function (profile_id) {
                /*
                (int) -> Promise Object
                Loads the barbones profile based on the MySQL database
                 */
                return httpHandler.request(loadBasicUser, {
                    profile_id: profile_id
                });
            },
            userActions: function(profile_id) {
                /*
                (int) -> Promise Object
                Attempts to return the array of actions the user has done
                 */
                return httpHandler.request(getUserActions, {
                    user_id: profile_id
                });
            }
        };
    }]).factory('profileHelper', ['serviceDate', 'actionHelper', function(serviceDate, actionHelper){
        return {
            profileMySQLFieldsToJS: function(profileObject) {
                if (angular.isObject(profileObject) && !Array.isArray(profileObject)) {
                    profileObject.date_registered = serviceDate.MySQLDatetimeToDateObject(profileObject.date_registered),
                    profileObject.last_seen = serviceDate.MySQLDatetimeToDateObject(profileObject.last_seen);
                }
                return profileObject;
            },
            actionMySQLFieldstoJS: function(ArrayofActions) {
                if (Array.isArray(ArrayofActions)) {
                    for (var i = 0; i < ArrayofActions.length; i++) {
                        //console.log(ArrayofActions[i]);
                        ArrayofActions[i].date_action = serviceDate.MySQLDatetimeToDateObject(ArrayofActions[i].date_action);
                        //Generate the message too
                        ArrayofActions[i] = actionHelper.formatActionMessage(ArrayofActions[i]);
                    }
                }
                return ArrayofActions;
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
    }).factory('userDropdown', ['httpHandler', 'getUserDropdown', function(httpHandler, getUserDropdown){
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
    }]).controller('controller', ['$scope', '$http', 'profileFactory', 'profileHelper', 'userDropdown', 'notificationsFactory', 'serviceDate', 'moment', '$window', function($scope, $http, profileFactory, profileHelper, userDropdown, notificationsFactory, serviceDate, moment, $window) {
    $scope.data = {}, //Holding object for scopes
    $scope.data.animationsEnabled = true;
    $scope.data.badmintonDates = [
    ]

    //Sidebar code
    $scope.toggle = true;

    $scope.toggleSidebar = function() {
        $scope.toggle = !$scope.toggle;
    }

    $scope.readAllNotifications = function() {
        notificationsFactory.ReadAll().then(function(successResponse) {
            console.log(successResponse);
            $window.location.reload();
        }, function(errorResponse) {
            console.log(errorResponse);
        });
    }

    notificationsFactory.CurrentUserNotifications().then(function(successResponse) {
        console.log(successResponse);
        $scope.data.dropdownNotifications = successResponse.data;
        $scope.data.newNotifications = 0;
        for (var i = 0; i < $scope.data.dropdownNotifications.length; i++) {
            //console.log($scope.data.notifications[i]);
            if ($scope.data.dropdownNotifications[i].read_status == 0) {
                $scope.data.newNotifications++;
                $scope.data.dropdownNotifications[i].style = {
                    "background-color": 'rgba(41, 128, 185, 0.1)'
                };
            }
            else {
                $scope.data.dropdownNotifications[i].style = '';
            }
        }
    }, function(errorResponse) {
        console.log(errorResponse);
    });

    notificationsFactory.CurrentUserNotifications().then(function(successResponse) {
        console.log(successResponse);
        $scope.data.notifications = [];
        var notifications = successResponse.data;
        var storedDates = [];
        //Group the notifications by date
        for (var i = 0; i < notifications.length; i++) {
            var date = serviceDate.MySQLDatetimeToDateObject(notifications[i].date_notified);
            var arrayDate = 
            notifications[i].date_notified = date;
            var niceDate = moment(date).format('MMMM Do YYYY');
            notifications[i].nice_date = niceDate;
            if (storedDates.indexOf(niceDate) == -1) {
                length = storedDates.push(niceDate);
                index = length - 1;
                $scope.data.notifications[index] = [];
            }
            else {
                index = storedDates.indexOf(niceDate);
            }
            $scope.data.notifications[index].push(notifications[i]);
        }
        console.log(storedDates);
        console.log($scope.data.notifications);
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
}]);
