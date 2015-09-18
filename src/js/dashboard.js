angular.
	module("RDash",["ui.bootstrap"]);
/*
function rdLoading(){
	var d=
	{
		restrict:"AE",
		template:'<div class="loading"><div class="double-bounce1"></div><div class="double-bounce2"></div></div>'
	};
	return d
}
angular.module("RDash").directive("rdLoading",rdLoading);

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
angular.module("RDash").directive("rdWidgetBody",rdWidgetBody);

function rdWidgetFooter(){
	var e=
	{
		requires:"^rdWidget",
		transclude:!0,
		template:'<div class="widget-footer" ng-transclude></div>',
		restrict:"E"
	};
return e}
angular.module("RDash").directive("rdWidgetFooter",rdWidgetFooter);

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
angular.module("RDash").directive("rdWidgetHeader",rdWidgetTitle);
*/
/*
function AlertsCtrl(e){
	e.alerts=[
		{
			type:"success",
			msg:"Thanks for visiting! Feel free to create pull requests to improve the dashboard!"
		},
		{
			type:"danger",
			msg:"Found a bug? Create an issue with as many details as you can."
		}
	],
	e.addAlert=function() {
		e.alerts.push({
			msg:"Another alert!"
		})
	},
	e.closeAlert= function(t){
		e.alerts.splice(t,1)
	}
}
angular.module("RDash").controller("AlertsCtrl",["$scope",AlertsCtrl]);
*/
angular.module("RDash").controller('controller', ["$scope", function($scope) {
	
}]);