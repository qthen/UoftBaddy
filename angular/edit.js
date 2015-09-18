angular./**
* app Module
*
* Description
*/
module('app', ['ui.bootstrap', 'ui.calendar', 'ngDialog', 'ngSanitize', 'angularMoment'])
    .config(function($provide) {
        $provide.constant('createConfirmedTime', 'html/ngDialog/create_date.html');
        $provide.constant('createConfirmedTimePHP', 'postRequests/user/createBadmintonDate.php');
        $provide.constant('loadBasicUser', 'postRequests/user/loadCurrentUser.php');
        $provide.constant('createTentativeDate', 'html/ngDialog/create_tentative_date.html');
        $provide.constant('markUnavailable', 'html/ngDialog/mark_unavailable_date.html');
        //$provide.constant('loadUser', 'postRequests/user/loadCurrentUser.php');
        $provide.constant('getUserActions', 'postRequests/user/getUserActions.php');
        $provide.constant('getUserEvents', 'postRequests/user/getUserEvents.php');
        $provide.constant('getCreatedEvents', 'postRequests/user/getCreatedUserEvents.php');
        $provide.constant('getUserDropdown', 'postRequests/user/getUserDropdown.php');
        $provide.constant('editProfile', 'postRequests/user/editProfileFields.php');
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
    }]).service('editService', ['httpHandler', 'editProfile', function(httpHandler, editProfile){
    	this.editCurrentUserProfile = function(editObject) {
    		/*
    		(Object for edits) -> Promise
    		*/
    		return httpHandler.request(editProfile, editObject);
    	}
    	
    }]).factory('profileFactory', ['$http', 'httpHandler', 'loadBasicUser', function($http, httpHandler, loadBasicUser){
        return {
            barebonesProfile: function () {
                /*
                (int) -> Promise Object
                Loads the barbones profile based on the MySQL database
                 */
                return httpHandler.request(loadBasicUser, {
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
    }).controller('controller', ['$scope', 'profileFactory', 'profileHelper', 'userDropdown', 'editService', function($scope, profileFactory, profileHelper, userDropdown, editService) {

    $scope.user = {};

    $scope.getUserProfile = function() {
    	profileFactory.barebonesProfile().then(function(successResponse) {
    		$scope.user = successResponse.data;
    	}, function(errorResponse) {
    		console.log(errorResponse);
    		$scope.errorMessage = 'No user found';
    	});
    }
    $scope.getUserProfile();

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
