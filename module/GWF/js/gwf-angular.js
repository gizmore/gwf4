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
		mainContent: 'aaaa',
		topContent: '',
		leftContent: '',
		rightContent: '',
		bottomContent: '',
	};
	
	$scope.requestPage = function(url) {
		console.log('GWFCtrl.requestPage()', url);
		RequestSrvc.send(url).then($scope.pageRequested.bind($scope, 'main'));
		return false;
	};

	$scope.pageRequested = function(bar, result) {
		console.log('GWFCtrl.pageRequested()', bar, result.data);
		$scope.data.mainContent = result.data;
		setTimeout(function(){
			RequestSrvc.fixForms($scope, bar, '.gwf-'+bar+'-content FORM');
			RequestSrvc.fixAnchors($scope, '.gwf-'+bar+'-content A');
		}, 1);
	};

	$scope.formRequested = function(bar, result) {
		console.log('GWFCtrl.formRequested()', bar, result);
		$scope.data[bar+'Content'] = result.data;
		setTimeout(function(){
			RequestSrvc.fixForms($scope, bar, '.gwf-'+bar+'-content FORM');
			RequestSrvc.fixAnchors($scope, '.gwf-'+bar+'-content A');
		}, 1);
		
		setTimeout(function(){
			SidebarSrvc.refreshSidebarsFor($scope);
		}, 3000);
	};
	
	$scope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams) {
		if (toState.name === 'loading') {
			RequestSrvc.fixForms($scope, 'main', 'FORM');
			RequestSrvc.fixAnchors($scope, 'A');
			PingSrvc.ping().then($scope.pageRequested.bind($scope, 'main'));
			SidebarSrvc.refreshSidebarsFor($scope);
		}
	});
	
	$scope.refreshedSidebar = function(bar, result) {
		console.log('GWFCtrl.refreshedSidebar()', bar, result);
		$scope.data[bar+'Content'] = result.data;
		setTimeout(function() {
			RequestSrvc.fixForms($scope, bar, '.gwf-'+bar+'-content FORM');
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
