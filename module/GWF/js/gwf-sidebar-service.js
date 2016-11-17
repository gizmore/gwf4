'use strict';
angular.module('gwf4').
service('SidebarSrvc', function($state, RequestSrvc) {

	var SidebarSrvc = this;
	
	SidebarSrvc.refreshSidebarsFor = function($scope) {
		console.log('SidebarSrvc.refreshSidebarsFor()');
		SidebarSrvc.refreshSidebars();
	};
	
	SidebarSrvc.refreshSidebars = function() {
		console.log('SidebarSrvc.refreshSidebars()');
	};

});
