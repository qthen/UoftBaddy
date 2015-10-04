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
        $provide.constant('getUserNotifications', 'postRequests/user/getUserNotifications.php');
        $provide.constant('markNotificationAsRead', 'postRequests/user/MarkNotificationAsRead.php');
        $provide.constant('leaveBadmintonDate', 'postRequests/user/leaveBadmintonDate.php');
        $provide.constant('withdrawAbsence', 'postRequests/user/withdrawAbsence.php');
        $provide.constant('notifiedAbsenceHTML', 'html/ngDialog/notified_absence.html');

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
            console.log(conversationObject);
            console.log(commentText);
            return httpHandler.request(postCommentInConversation, {
                message_text: commentText,
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
    }]).factory('dateHelper', ['serviceDate', 'moment', 'httpHandler', 'leaveBadmintonDate', 'withdrawAbsence', function(serviceDate, moment, httpHandler, leaveBadmintonDate, withdrawAbsence){
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
                dateObject.begin_date = moment(dateObject.begin_datetime).calendar();
                dateObject.in_about = moment(dateObject.begin_datetime).fromNow();
                dateObject.in_about = dateObject.in_about.substr(0, 1).toUpperCase() + dateObject.in_about.substr(1);
                dateObject.message = 'Court booked until ' + moment(dateObject.end_datetime).format('h:mm a');
                return dateObject;
            },
            formatConversationMessage: function(ArrayOfMessages) {
                for (var i = 0; i < ArrayOfMessages.length; i++) {
                    var message = moment(ArrayOfMessages[i].date_posted).fromNow();
                    message = message.substr(0, 1).toUpperCase() + message.substr(1);
                    ArrayOfMessages[i].moment_posted = message;
                }
                return ArrayOfMessages;
            },
            NotifyAbsence: function(date_id) {
                /*
                (Int) -> Promise Object
                Stores in the database that you will be absent and returns the promise from the $http service
                */
                return httpHandler.request(leaveBadmintonDate, {
                    date_id: date_id
                });
            },
            Withdraw: function(date_id) {
                /*
                (Int) -> Promise Object
                Attempts to withdraw the absence from the database
                */
                return httpHandler.request(withdrawAbsence, {
                    date_id: date_id
                });
            } 
        }
    }]).factory('dateFactory', ['$http', 'httpHandler', 'getBadmintonDate', 'getMessagesFromDate', 'serviceDate', function($http, httpHandler, getBadmintonDate, getMessagesFromDate, serviceDate){
        var factoryDate = {};

        factoryDate.barebonesBadmintonDate = function(date_id) {
            /*
            Get the date basics
             */
             console.log(date_id);
            return httpHandler.request(getBadmintonDate, {
                date_id: date_id
            });
        }

        factoryDate.badmintonDateConversation = function(date_id) {
            /*
            (Int) -> Convesation Object
            */
            return httpHandler.request(getMessagesFromDate, {
                date_id: date_id
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
    }).controller('controller', ['$scope', '$http', 'getBadmintonDate', 'getThatDayDates', 'getThatWeekDates', 'getMessagesFromDate', 'MySQLtoJS', 'joinBadmintonDate', 'dateFactory', 'dateHelper', 'userDropdown', 'uiFactory', 'conversationService', 'ngDialog', 'ngDialogCloseConversation', 'serviceDate', 'ngDialog', '$window', 'notificationsFactory', 'notifiedAbsenceHTML', function($scope, $http, getBadmintonDate, getThatDayDates, getThatWeekDates, getMessagesFromDate, MySQLtoJS, joinBadmintonDate, dateFactory, dateHelper, userDropdown, uiFactory, conversationService, ngDialog, ngDialogCloseConversation, serviceDate, ngDialog, $window, notificationsFactory, notifiedAbsenceHTML){

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

    $scope.withdrawAbsence = function() {
        dateHelper.Withdraw($scope.data.badmintonDate.date_id).then(function(successResponse) {
            console.log(successResponse);
        }, function(errorResponse) {
            console.log(errorResponse);
        })
    }

    $scope.propogateRead = function(notificationObject) {
        notificationsFactory.MarkAsRead(notificationObject).then(function(successResponse) {
            $window.location.href = '/' + notificationObject.a_href;
        }, function(errorResponse) {
            alert('Some error occured in handling notifications');
            console.log(errorResponse);
        });
    }

    $scope.notifyAbsence = function() {
        dateHelper.NotifyAbsence($scope.data.badmintonDate.date_id).then(function(successResponse) {
            console.log(successResponse);
            var dialog = ngDialog.open({
                template: notifiedAbsenceHTML
            });
            dialog.closePromise.then(function() {
                $window.location.reload();
            })
        }, function(errorResponse) {
            console.log(errorResponse);
        })
    }
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
                var dialogPromise = ngDialog.open({
                    template: 'html/ngDialog/success_join.html'
                });
                dialogPromise.closePromise.then(function() {
                    $window.location.reload();
                });

            }, function(errorResponse) {
                console.log(errorResponse);
            });
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
                console.log(successResponse);

                var badmintonDate = successResponse.data;

                //Convert the badminton date fields into the JS dates
                badmintonDate = dateHelper.dateMySQLFieldstoJS(badmintonDate);
                //console.log(badmintonDate);
                $scope.data.badmintonDate = dateHelper.formatDateMessage(badmintonDate);
                //Get the date's conversation
                
                dateFactory.badmintonDateConversation($scope.data.badmintonDate.date_id).then(function(successResponse) {
                    console.log(successResponse);
                    var messages = successResponse.data.messages;

                    for (var i = 0; i < messages.length; i++) {
                        messages[i].date_posted = serviceDate.MySQLDatetimeToDateObject(messages[i].date_posted);
                    }

                    var conversation = successResponse.data;
                    conversation.messages = messages;
                    console.log(conversation);

                    $scope.data.badmintonDate.conversation = conversation;
                    //Format the moment messages for the date
                    $scope.data.badmintonDate.conversation.messages = dateHelper.formatConversationMessage($scope.data.badmintonDate.conversation.messages);

                    //Finally check if the current user id is joined
                    for (var i = 0; i < $scope.data.badmintonDate.attendees.length; i++) {
                        if ($scope.data.badmintonDate.attendees[i].user_id == $scope.user.user_id) {
                            $scope.data.badmintonDate.joined = true;
                        }
                        $scope.data.badmintonDate.attendees[i].date_joined = serviceDate.MySQLDatetimeToDateObject($scope.data.badmintonDate.attendees[i].date_joined);
                    }

                    for (var i = 0; i <  $scope.data.badmintonDate.absences.length; i++) {
                        if ($scope.data.badmintonDate.absences[i].user_id == $scope.user.user_id) {
                            $scope.data.badmintonDate.left = true;
                        }
                        $scope.data.badmintonDate.absences[i].date_joined = serviceDate.MySQLDatetimeToDateObject($scope.data.badmintonDate.absences[i].date_joined);
                    }

                    //Get the label class
                    $scope.data.badmintonDate.label_class = ($scope.data.badmintonDate.begin_datetime > new Date()) ? 'label label-success' : 'label label-danger';
                    $scope.data.badmintonDate.label_message =  ($scope.data.badmintonDate.begin_datetime > new Date()) ? 'Upcoming' : 'Past';

                }, function(errorResponse) {
                    console.log(errorResponse);
                    $scope.data.badmintonDate.conversation = [];
                });

                console.log($scope.data.badmintonDate);
            }, function(errorResponse) {
                console.log(errorResponse);
            });
        }
        
    }]);