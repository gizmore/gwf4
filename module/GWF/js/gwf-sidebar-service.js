'use strict';
angular.module('gwf4').
service('SidebarSrvc', function(RequestSrvc) {

	var SidebarSrvc = this;
	
	SidebarSrvc.LISTENERS = [];
	
	SidebarSrvc.refreshSidebarsFor = function($scope, sidebar) {
//		console.log('SidebarSrvc.refreshSidebarsFor()');
		if (SidebarSrvc.LISTENERS.length == 0) {
			SidebarSrvc.LISTENERS.push($scope);
		}
		SidebarSrvc.refreshSidebar(sidebar);
	};
	
	SidebarSrvc.refreshSidebar = function(sidebar) {
//		console.log('SidebarSrvc.refreshSidebar()');
		sidebar = sidebar || 'top,left,right,bottom';
		var url = GWF_WEB_ROOT + 'index.php?mo=GWF&me=AngularSidebar&ajax=1&bar=' + sidebar;
		return RequestSrvc.send(url).then(function(result) {
			for (var i in SidebarSrvc.LISTENERS) {
				var $scope = SidebarSrvc.LISTENERS[i];
				$scope.refreshedSidebar(sidebar, result);
			}
		});
	};

});
