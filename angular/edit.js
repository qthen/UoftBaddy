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
        $provide.constant('loadUser', 'postRequests/profile/loadUser.php');
        $provide.constant('getUserActions', 'postRequests/user/getUserActions.php');
        $provide.constant('getUserEvents', 'postRequests/user/getUserEvents.php');
        $provide.constant('getCreatedEvents', 'postRequests/user/getCreatedUserEvents.php');
        $provide.constant('getUserDropdown', 'postRequests/user/getUserDropdown.php');
        $provide.constant('editProfileFields', 'postRequests/user/editProfileFields.php');
        $provide.constant('SuccessfullyEdittedProfileDialog', 'html/ngDialog/success_edit_profile.html');
        $provide.constant('PlayingLevelChoices', [
            {
                choice_id: 0,
                choice_name: 'Unspecified'
            },
            {
                'choice_id': 1,
                'choice_name': 'Beginner'
            },
            {
                'choice_id': 2,
                'choice_name': 'Casual Intermediate'
            },
            {
                'choice_id': 3,
                'choice_name': 'Frequent Intermediate'
            },
            {
                'choice_id': 4,
                'choice_name': 'Low-Level Advanced'
            },
            {
                'choice_id': 5,
                'choice_name': 'Intermediate Advanced'
            },
            {
                'choice_id': 6,
                'choice_name': 'High-Level Advanced'
            },
            {
                'choice_id': 7,
                'choice_name': 'Provincially Ranked'
            },
            {
                'choice_id': 8,
                'choice_name': 'Internationally Ranked'
            }
        ]);
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
    }]).service('actionHelper', ['moment', function(moment){
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
        
    }]).service('ProfileEdit', ['httpHandler', 'editProfileFields', function(httpHandler, editProfileFields){
        this.AttemptEditOnSelf = function(EdittedProfile) {
            /*
            (EditedProfile) -> Promise
            */
            console.log(EdittedProfile);
            return httpHandler.request(editProfileFields, EdittedProfile);
        }
    }]).factory('profileFactory', ['$http', 'httpHandler', 'loadBasicUser', 'getUserActions', function($http, httpHandler, loadBasicUser, getUserActions){
        return {
            barebonesProfile: function (profile_id) {
                /*
                (int) -> Promise Object
                Loads the barbones profile based on the MySQL database
                 */
                return httpHandler.request(loadBasicUser, {});
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
    }).controller('controller', ['$scope', '$http', 'profileFactory', 'profileHelper', 'userDropdown', 'ProfileEdit', '$window', 'ngDialog', 'SuccessfullyEdittedProfileDialog', 'PlayingLevelChoices', function($scope, $http, profileFactory, profileHelper, userDropdown, ProfileEdit, $window, ngDialog, SuccessfullyEdittedProfileDialog, PlayingLevelChoices) {
    $scope.data = {}, //Holding object for scopes
    $scope.data.animationsEnabled = true;
    $scope.data.badmintonDates = [
    ];

    $scope.data.playingLevels = PlayingLevelChoices;
    console.log($scope.data.playingLevels);

    function EdittedProfile(ProgramOfStudy, IntCommuter, Bio, Accolades, PlayingLevelInt) {
        this.bio = Bio;
        this.program = ProgramOfStudy;
        this.commuter = IntCommuter;
        this.level = PlayingLevelInt;
        if (!this.level || this.level == 'Not Disclosed') {
            this.level = 0;
        }
        this.accolades = Accolades;
    }

    $scope.editProfile = function() {
        /*
        Attempts to send a request edit on the currently logged in user
        */
        var NewProfile = new EdittedProfile($scope.profile.program, $scope.profile.int_commuter, $scope.profile.bio, $scope.profile.accolades, $scope.profile.level);

        var AttemptEdit = ProfileEdit.AttemptEditOnSelf(NewProfile).then(function(successResponse) {
            console.log(successResponse);
            var dialog =  ngDialog.open({
                template: SuccessfullyEdittedProfileDialog
            });
            dialog.closePromise.then(function() {
                $window.location.href = '/profile.php?id=' + $scope.profile.user_id;
            })
        }, function(errorResponse) {
            console.log(errorResponse);
        });
    }

    //Sidebar code
    $scope.toggle = true;

    $scope.toggleSidebar = function() {
        $scope.toggle = !$scope.toggle;
    }

    profileFactory.barebonesProfile().then(function(successResponse) {
        $scope.profile = successResponse.data;
        console.log($scope.profile);
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
