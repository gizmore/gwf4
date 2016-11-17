'use strict'
angular.module('gwf4', ['ngMaterial', 'ui.router']).
config(function($urlRouterProvider, $stateProvider) {
	
	$stateProvider.state({
		name: 'loading',
		url: '/loading',
		controller: 'LoadingCtrl',
		templateUrl: GWF_CONFIG.WEB_ROOT+'module/GWF/js/tpl/loading.html',
		pageTitle: 'Loading'
	});
	$urlRouterProvider.otherwise('/loading');
}).
run(function($state, PingSrvc) {
}).
controller('GWFCtrl', function($scope, $sce, $mdSidenav, ErrorSrvc, PingSrvc, RequestSrvc, SidebarSrvc) {
	
	$scope.data = {
		user: GWF_USER,
		content: '<div id="gwf-loading-text">Loading</div>',
	};
	
	$scope.requestPage = function(url) {
		console.log('GWFCtrl.requestPage()', url);
		RequestSrvc.send(url).then($scope.pageRequested);
		return false;
	};

	$scope.pageRequested = function(result) {
		console.log(result);
		$scope.data.content = result.data;
//		RequestSrvc.fixAnchors($scope, '#gwf-dynamic-content A');
	};
	
	$scope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams) {
		if (toState.name === 'loading') {
			RequestSrvc.fixAnchors($scope, 'A');
			PingSrvc.ping().then($scope.pageRequested);
			SidebarSrvc.refreshSidebars($scope);
		}
	});

	$scope.toggleLeftMenu = function() {
		$mdSidenav('left').toggle();
	};

	$scope.toggleRightMenu = function() {
		$mdSidenav('right').toggle();
	};
}).
controller('LoadingCtrl', function($scope) {
});
