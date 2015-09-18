angular./**
* app Module
*
* Description
*/
module('app', ['ui.bootstrap', 'angularMoment', 'ngDialog'])
    .config(function($provide) {
        $provide.constant('getBadmintonDate', 'postRequests/date/getBadmintonDate.php');
        $provide.constant('getThatDayDates', 'postRequests/date/getBadmintonDateByDay.php');
        $provide.constant('getThatWeekDates', 'postRequests/date/getBadmintonDateByWeek.php');
        $provide.constant('getMessagesFromDate', 'postRequests/date/getMessagesFromDate.php');
        $provide.constant('joinBadmintonDate', 'postRequests/user/joinBadmintonDate.php');
        $provide.constant('getUserDropdown', 'postRequests/user/getUserDropdown.php');
        $provide.constant('loadUIUser', 'postRequests/profile/loadUser.php');

        $provide.constant('postCommentInConversation', 'postRequests/user/postMessage.php');
        $provide.constant('postReplyConversation', 'postRequests/user/postReplyMessage.php');

        $provide.constant('closeConversation', 'postRequests/user/closeConversation.php');

        $provide.constant('ngDialogCloseConversation', 'html/ngDialog/close_conversation.html');

        $provide.value('MySQLtoJS', function(datetimeString) {
            var t = datetimeString.split(/[- :]/);
            var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
            return d;
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
    }]).factory('uiFactory', ['httpHandler', 'loadUIUser', function(httpHandler, loadUIUser) {
        return {
            headerUser: function(user_id) {
                return httpHandler.request(loadUIUser, {
                    profile_id: user_id
                });
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
    }]).service('conversationService', ['httpHandler', 'postCommentInConversation', 'postReplyConversation', 'closeConversation', function(httpHandler, postCommentInConversation, postReplyConversation, closeConversation){
        this.postComment = function(conversationObject, commentText) {
            /*
            (Conversation, String) -> Promise
            */
            return httpHandler.request(postCommentInConversation, {
                comment_text: commentText,
                conversation_id: conversationObject.conversation_id
            });
        }

        this.replyComment = function(conversationMessage, replyText) {
            /*
            (Message) -> Promise
            */
            return httpHandler.request(postConversationReply, {
                parent_id: conversationMessage.message_id,
                message_text: replyText
            });
        }
        
        this.close = function(ConversationObject) {
            /*
            (Conversation) -> Promise
            */
            return httpHandler.request(closeConversation, {
                conversation_id: ConversationObject.conversation_id
            });
        }
    }]).factory('dateHelper', ['serviceDate', 'moment', function(serviceDate, moment){
        return {
            dateMySQLFieldstoJS: function(dateObject) {
                /*
                (Date) -> Date
                Converts the MySQL datetime fields of the date object into JS date objects
                */
                dateObject.begin_datetime = serviceDate.MySQLDatetimeToDateObject(dateObject.begin_datetime),
                dateObject.end_datetime = serviceDate.MySQLDatetimeToDateObject(dateObject.end_datetime);
                return dateObject;
            },
            formatDateMessage: function(dateObject) {
                /*
                (Date) -> Date
                Formats the message based on the date fields of the object, should be in JS Date form
                */
                if (dateObject.begin_datetime >= new Date()) {
                    dateObject.message = 'Begins at ' + moment(dateObject.begin_datetime).format('MMMM Do YYYY, h:mm a') + ' and likely ends at ' + moment(dateObject.end_datetime).format('MMMM Do YYYY, h:mm a');
                }
                else {
                    dateObject.message = 'Began at ' + moment(dateObject.begin_datetime).format('MMMM Do YYYY, h:mm a') + ' and likely ended at ' + moment(dateObject.end_datetime).format('MMMM Do YYYY, h:mm a');
                }
            },
            formatConversationMessage: function(ArrayOfMessages) {
                for (var i = 0; i < ArrayOfMessages.length; i++) {
                    ArrayOfMessages[i].moment_posted = moment(ArrayOfMessages[i].date_posted).fromNow();
                }
                return ArrayOfMessages;
            }
        }
    }]).factory('dateFactory', ['$http', 'httpHandler', 'getBadmintonDate', 'getMessagesFromDate', 'serviceDate', function($http, httpHandler, getBadmintonDate, getMessagesFromDate, serviceDate){
        var factoryDate = {};

        factoryDate.barebonesBadmintonDate = function(date_id) {
            /*
            Get the date basics
             */
            return httpHandler.request(getBadmintonDate, {
                date_id: date_id
            });
        }

        factoryDate.badmintonDateConversation = function(date_id) {
            /*
            (Int) -> Convesation Object
            */
            httpHandler.request(getMessagesFromDate, {
                date_id: date_id
            }).then(function(successResponse) {
                var messages = successResponse.data.messages;

                for (var i = 0; i < messages.length; i++) {
                    messages[i].date_posted = serviceDate.MySQLDatetimeToDateObject(messages[i].date_posted);
                }

                var conversation = successResponse.data;
                conversation.messages = messages;

                return conversation;
            }, function(errorResponse) {
                console.log(errorResponse);
                return {};
            });
        }
        return factoryDate;
    }])
    .directive("rdWidget", function() {
        var d={
            transclude:!0,
            template:'<div class="widget" ng-transclude></div>',
            restrict:"EA"
        };
        return d
    }).directive("rd-loadingading", function() {
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
    }).controller('controller', ['$scope', '$http', 'getBadmintonDate', 'getThatDayDates', 'getThatWeekDates', 'getMessagesFromDate', 'MySQLtoJS', 'joinBadmintonDate', 'dateFactory', 'dateHelper', 'userDropdown', 'uiFactory', 'conversationService', 'ngDialog', 'ngDialogCloseConversation', function($scope, $http, getBadmintonDate, getThatDayDates, getThatWeekDates, getMessagesFromDate, MySQLtoJS, joinBadmintonDate, dateFactory, dateHelper, userDropdown, uiFactory, conversationService, ngDialog, ngDialogCloseConversation){

        $scope.data = {};

        //Conversation scope functions
        $scope.postMessage = function() {
            if ($scope.data.possibleMessage) {
                conversationService.postComment($scope.data.badmintonDate.conversation, $scope.data.possibleMessage).then(function(successResponse) {
                    $scope.data.badmintonDate.conversation.messages.push(successResponse.data);
                }, function(errorResponse) {
                    console.log(errorResponse);
                });
            }
        }

        $scope.postReply = function(messageBeingReplied) {
            for (var i = 0; i < $scope.data.badmintonDate.conversation.messages.length; i++) {
                if ($scope.data.badmintonDate.conversation.messages[i].message_id == messageBeingReplied.message_id) {
                    if ($scope.data.badmintonDate.conversation.messages[i].possibleReply) {
                        conversationService.replyComment(messageBeingReplied, $scope.data.badmintonDate.conversation.messages[i].possibleReply).then(function(successResponse) {
                        $scope.data.badmintonDate.conversation.messages[i].replies.push(successResponse.data);
                        }, function(errorResponse) {
                            console.log(errorResponse);
                        })
                        break;
                    }
                }
            }
        }

        //Header bar code
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

        /*Sidebar code */
        $scope.toggle = true; //By default
        $scope.toggleSidebar = function() {
            /*
            Had to simplify a lot of routing
             */
            $scope.toggle = !$scope.toggle;
        }
        $scope.join = function() {
            var promiseJoin = $http({
                method: "post",
                url: joinBadmintonDate,
                data: {
                    date_id: $scope.data.badmintonDate.date_id
                },
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).then(function(successResponse) {
                console.log(successResponse);
            }, function(errorResponse) {
                console.log(errorResponse);
            })
        }

        $scope.promptConfirmation = function() {
            var dialog = ngDialog.open({
                template: ngDialogCloseConversation,
                scope: $scope,
                controller: ['$scope', 'conversationService', function($scope, conversationService) {
                    $scope.data = {};
                    $scope.data.conversation = $scope.$parent.data.badmintonDate.conversation;

                    $scope.closeConversation = function() {
                        conversationService.close($scope.data.conversation).then(function(successResponse) {
                            console.log(successResponse);
                        }, function(errorResponse) {
                            console.log(errorResponse);
                        });
                    }
                }]
            });
        }

        $scope.getMessages = function(date_id) {
            /*
            (Str) -> Null
             */
            var promiseGetMessages = $http({
                method: "post",
                url: getMessagesFromDate,
                data: {
                    date_id: date_id
                },
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).then(function(successResponse) {
                $scope.data.messages = successResponse.data.messages;
                for (var i = 0; i < $scope.data.messages.length; i++) {
                    $scope.data.messages[i].date_posted = MySQLtoJS($scope.data.messages[i].date_posted);
                }
                console.log(successResponse);
            }, function(errorResponse) {
                console.log(errorResponse);
            });
        }


        $scope.getSideBar = function(dateString) {
            /*
            (Str) -> Bool
            Fetches the sidebar of the badminton date
             */
            var promiseGetThatDay = $http({
                method: "post",
                url: getThatDayDates,
                data: {
                    date: dateString
                },
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).then(function(successResponse) {
                $scope.data.thatDay = successResponse.data;
            }, function(errorResponse) {
                console.log(errorResponse);
            });

            var promiseGetThatWeek = $http({
                method: "post",
                url: getThatWeekDates,
                data: {
                    date: dateString
                },
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).then(function(successResponse) {
                $scope.data.thatWeek = successResponse.data;
            }, function(errorResponse) {
                console.log(errorResponse);
            });
        }

        $scope.init = function(date_id, user_id) {
            console.log(date_id);
            $scope.data = {};

            uiFactory.headerUser(user_id).then(function(successResponse) {
                $scope.user = successResponse.data;
            }, function(errorResponse) {
                console.log(errorResponse);
            })

            //Get the date via the factory method
            dateFactory.barebonesBadmintonDate(date_id).then(function(successResponse) {
                //console.log(successResponse);

                var badmintonDate = successResponse.data;

                //Convert the badminton date fields into the JS dates
                badmintonDate = dateHelper.dateMySQLFieldstoJS(badmintonDate);
                $scope.data.badmintonDate = dateHelper.formatDateMessage(badmintonDate);

                //Get the date's conversation
                $scope.data.badmintonDate.conversation = dateFactory.badmintonDateConversation($scope.data.badmintonDate.date_id);

                //Format the moment messages for the date
                $scope.data.badmintonDate.conversation.messages = dateHelper.formatConversationMessage($scope.data.badmintonDate.conversation.messages);
                console.log($scope.data.badmintonDate);
            }, function(errorResponse) {
                console.log(errorResponse);
            });
        }
        
    }]);