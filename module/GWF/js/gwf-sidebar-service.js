'use strict';
angular.module('gwf4').
service('SidebarSrvc', function($state, RequestSrvc) {

	var SidebarSrvc = this;
	
	SidebarSrvc.LISTENERS = [];
	
	SidebarSrvc.refreshSidebarsFor = function($scope) {
		console.log('SidebarSrvc.refreshSidebarsFor()');
		if (SidebarSrvc.LISTENERS.length == 0) {
			SidebarSrvc.LISTENERS.push($scope);
		}
		SidebarSrvc.refreshSidebar('top,left,right,bottom');
	};
	
	SidebarSrvc.refreshSidebar = function(sidebar) {
		console.log('SidebarSrvc.refreshSidebar()', sidebar);
		RequestSrvc.requestPage('GWF', 'AngularSidebar', { bar: sidebar }).then(function(result) {
			for (var i in SidebarSrvc.LISTENERS) {
				var $scope = SidebarSrvc.LISTENERS[i];
				$scope.refreshedSidebar(sidebar, result);
			}
		});
				
	};

});
