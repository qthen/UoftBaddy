angular./**
* app Module
*
* Description
*/
module('app', []).config(function($provide) {
	$provide.constant('getUserConversations', 'postRequests/user/getUserConversations.php');
	$provide.constant('getMessages', 'postRequests/user/getMessagesFromConversation.php');
	$provide.constant('postMessage', 'postRequests/user/postMessage.php');
	$provide.value('MySQLtoJS', function(datetimeString) {
		var t = datetimeString.split(/[- :]/);
		var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
		return d;
	});
}).controller('controller', ['$scope', '$http', 'getUserConversations', 'getMessages', 'postMessage', 'MySQLtoJS', function($scope, $http, getUserConversations, getMessages, postMessage, MySQLtoJS){
	$scope.data = {},
	$scope.data.currentView = null; //Stores the current view of the inbox, {} is default

	console.log($scope.data.currentView == {});

	$scope.pushMessage = function() {
		if ($scope.data.messageBox && $scope.data.currentView.conversation_id) {
			console.log($scope.data.currentView.conversation_id);
			console.log($scope.data.messageBox);
			var promisePushMesage = $http({
				method: "post",
				url: postMessage,
				data: {
					message_text: $scope.data.messageBox,
					conversation_id: $scope.data.currentView.conversation_id
				},

			}).then(function(successResponse) {
				console.log(successResponse);
				$scope.data.currentView.messages.push(successResponse.data);
				$scope.data.messageBox = '';
		}, function(errorResponse) {
				console.log(errorResponse);
			});
		}
	}
	
	var promiseGetInbox = $http({
		method: "post",
		url: getUserConversations,
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
	}).then(function(successResponse) {
		console.log(successResponse);
		$scope.data.conversations = successResponse.data;
		console.log($scope.data.conversations);
	}, function(errorResponse) {
		console.log(errorResponse);
	});

	$scope.createConvo = function() {
		$scope.data.newConvo = true;
	}

	$scope.openConversation = function(conversation) {
		console.log(conversation);
		/*
		Opens a conversation into the current view
		 */
		var promiseGetMessage = $http({
			method: "post",
			url: getMessages,
			data: {
				conversation_id: conversation.conversation_id
			},
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
		}).then(function(successResponse) {
			console.log(successResponse);
			for (var i = 0; i < $scope.data.conversations.length; i++) {
				if ($scope.data.conversations[i].conversation_id == conversation.conversation_id) {
					$scope.data.conversations[i].messages = successResponse.data.messages;
					angular.forEach($scope.data.conversations[i].messages, function(value, key) {
						value.date_posted = MySQLtoJS(value.date_posted);
					});
					console.log($scope.data.conversations[i]);
					$scope.switchView($scope.data.conversations[i]);
					break;
				}
			}
		}, function(errorResponse) {
			console.log(errorResponse);
		})
	}

	$scope.switchView = function(object) {
		$scope.data.currentView = object;
	}
}])