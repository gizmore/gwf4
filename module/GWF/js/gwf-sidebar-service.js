'use strict';
angular.module('gwf4').
service('SidebarSrvc', function($state, RequestSrvc) {

	var SidebarSrvc = this;
	
	SidebarSrvc.LISTENERS = [];
	
	SidebarSrvc.refreshSidebarsFor = function($scope) {
		console.log('SidebarSrvc.refreshSidebarsFor()');
		SidebarSrvc.LISTENERS.push($scope);
		SidebarSrvc.refreshSidebar('top');
		SidebarSrvc.refreshSidebar('left');
		SidebarSrvc.refreshSidebar('right');
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
