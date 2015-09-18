var app = angular./**
* app Module
*
* Description
*/
module('app', ['ui.bootstrap'])
    .config(function($provide) {
        $provide.constant('createConfirmedTime', 'html/ngDialog/create_date.html');
        $provide.constant('createConfirmedTimePHP', 'postRequests/user/createBadmintonDate.php');
        $provide.constant('loadBasicUser', 'postRequests/profile/loadUser.php');
        $provide.constant('createTentativeDate', 'html/ngDialog/create_tentative_date.html');
        $provide.constant('markUnavailable', 'html/ngDialog/mark_unavailable_date.html');
        $provide.constant('getAllUsers', 'postRequests/user/getAllUsers.php');
        $provide.constant('getUserStats', 'postRequests/user/getUserStats.php');
        $provide.constant('getUserEvents', 'postRequests/user/getUserEvents.php');
        $provide.constant('getCreatedEvents', 'postRequests/user/getCreatedUserEvents.php');
        $provide.value('MySQLtoJS', function(datetimeString) {
            var t = datetimeString.split(/[- :]/);
            var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
            return d;
        });


        $provide.value('convertMySQLToJS', function(arrayInput) {
            for (var i = 0; i < arrayInput.length; i++) {
                var t = arrayInput[i].begin_datetime.split(/[- :]/);
                var start = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
                arrayInput[i].begin_datetime = start;
                var t = arrayInput[i].end_datetime.split(/[- :]/);
                var end = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
                arrayInput[i].end_datetime = end;
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
        })
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
    }]).factory('usersFactory', ['serviceDate', 'getAllUsers', 'httpHandler', function(serviceDate, getAllUsers, httpHandler) {
        /*
        Primary factory for grabbing the data for the users page
         */
        return {
            fetchUsers: function() {
                /*
                (Null) -> Promise Object
                Factory function grabs the users table
                 */
                return httpHandler.request(getAllUsers, {
                    filter: 'reputation'
                });
            }
        }
    }]).factory('usersHelper', ['serviceDate', function(serviceDate){
        /*
        Factory helper for information grabber on users
         */
        return {
            formatUsersDateFields: function(ArrayofUsers) {
                /*
                (Array) -> Array
                Formats the user MySQL datetime fields into JS Date objects
                 */
                for (var i = 0; i < ArrayofUsers.length; i++) {
                    ArrayofUsers[i].date_registered = serviceDate.MySQLDatetimeToDateObject(ArrayofUsers[i].date_registered),
                    ArrayofUsers[i].last_seen = serviceDate.MySQLDatetimeToDateObject(ArrayofUsers[i].last_seen);
                }
                return ArrayofUsers;
            }
        }
    }])
    .directive("rdWidget", function() {
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
    }).controller('controller', ['$scope', '$http', 'usersFactory', 'usersHelper', function($scope, $http, usersFactory, usersHelper) {

    $scope.data = {};

    //Code for the sidebar

    $scope.toggle = true;
    
    $scope.toggleSidebar = function() {
        /*
        Had to simplify a lot of routing
         */
        $scope.toggle = !$scope.toggle;
    }

    //Get all the users
    usersFactory.fetchUsers().then(function(successResponse) {
        console.log(successResponse);
        var users = successResponse.data;

        //Now format it correctly
        $scope.data.users = usersHelper.formatUsersDateFields(users);
    }, function(errorResponse) {
        console.log(errorResponse);
    });

}]);
