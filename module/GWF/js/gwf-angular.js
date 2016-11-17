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
		mainContent: '<div id="gwf-loading-text">Loading</div>',
		topContent: '',
		leftContent: '',
		rightContent: '',
		bottomContent: '',
	};
	
	$scope.requestPage = function(url) {
		console.log('GWFCtrl.requestPage()', url);
		RequestSrvc.send(url).then($scope.pageRequested);
		return false;
	};

	$scope.pageRequested = function(result) {
		console.log('GWFCtrl.pageRequested()', result);
		$scope.data.content = result.data;
		setTimeout(function(){
			RequestSrvc.fixForms($scope, '.gwf-main-content FORM');
			RequestSrvc.fixAnchors($scope, '.gwf-main-content A');
		}, 1);
	};
	
	$scope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams) {
		if (toState.name === 'loading') {
			RequestSrvc.fixForms($scope, 'FORM');
			RequestSrvc.fixAnchors($scope, 'A');
			PingSrvc.ping().then($scope.pageRequested);
			SidebarSrvc.refreshSidebarsFor($scope);
		}
	});
	
	$scope.refreshedSidebar = function(bar, result) {
		console.log('GWFCtrl.refreshedSidebar()', bar, result);
		$scope.data[bar+'Content'] = result.data;
		setTimeout(function() {
			RequestSrvc.fixForms($scope, '.gwf-'+bar+'-content FORM');
			RequestSrvc.fixAnchors($scope, '.gwf-'+bar+'-content A');
		}, 1);
	};

	$scope.toggleLeftMenu = function() {
		$mdSidenav('left').toggle();
	};

	$scope.toggleRightMenu = function() {
		$mdSidenav('right').toggle();
	};
}).
controller('LoadingCtrl', function($scope) {
});
