'use strict'
angular.module('gwf4', ['ngMaterial', 'ui.router', 'flow']).
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
run(function($injector) {
	window.ANGULAR_INJECTOR = $injector; // Oops. Angular exposed to window.
}).
controller('GWFCtrl', function($scope, $state, $mdSidenav, ErrorSrvc, PingSrvc, RequestSrvc, SidebarSrvc) {
	
	$scope.data = {
		user: GWF_USER,
		mainContent: '',
		topContent: '',
		leftContent: '',
		rightContent: '',
		bottomContent: '',
	};
	
	$scope.requestState = function(name, params) {
		console.log('GWFCtrl.requestState()', name, params);
		$scope.hideGWFContent();
		return $state.go(name, params);
	};
	
	$scope.requestGWFPage = function(module, method, data) {
		console.log('GWFCtrl.requestGWFPage()', module, method, data);
		$scope.hideGWFContent();
		$scope.closeSidenavs();
		return RequestSrvc.requestPage(module, method, data).then($scope.pageRequested.bind($scope, 'main'));
	}

	$scope.requestPage = function(url) {
		console.log('GWFCtrl.requestPage()', url);
		$scope.hideGWFContent();
		$scope.closeSidenavs();
		RequestSrvc.send(url).then($scope.pageRequested.bind($scope, 'main'));
		return false;
	};

	$scope.pageRequested = function(bar, result) {
		console.log('GWFCtrl.pageRequested()', bar, result);
		$scope.data.mainContent = result.data;
		setTimeout(function(){
			RequestSrvc.fixForms($scope, bar, '.gwf-'+bar+'-content FORM');
			RequestSrvc.fixAnchors($scope, '.gwf-'+bar+'-content A');
			RequestSrvc.fixSelects($scope, '.gwf-'+bar+'-content SELECT');
		}, 1);
	};
	
	$scope.closeSidenavs = function() {
		console.log('GWFCtrl.closeSidenavs()');
		$mdSidenav('left').close();
		$mdSidenav('left').close();
	};
	
	$scope.hideGWFContent = function() {
		console.log('GWFCtrl.hideGWFContent()');
		jQuery('#gwf-page-content').hide();
	};
	
	$scope.focusMainContent = function() {
		$scope.closeSidenavs();
		$scope.hideGWFContent();
	};

	$scope.formRequested = function(bar, result) {
		console.log('GWFCtrl.formRequested()', bar, result);
		$scope.data[bar+'Content'] = result.data;
		$scope.closeSidenavs();
		setTimeout(function(){
			RequestSrvc.fixForms($scope, bar, '.gwf-'+bar+'-content FORM');
			RequestSrvc.fixAnchors($scope, '.gwf-'+bar+'-content A');
			RequestSrvc.fixSelects($scope, '.gwf-'+bar+'-content SELECT');
		}, 1);
		setTimeout(function(){
			SidebarSrvc.refreshSidebarsFor($scope);
		}, 3000);
	};
	
	$scope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams) {
		if (toState.name === 'loading') {
			RequestSrvc.fixForms($scope, 'main', 'FORM');
			RequestSrvc.fixAnchors($scope, 'A');
			RequestSrvc.fixSelects($scope, 'SELECT');
			PingSrvc.ping().then($scope.pageRequested.bind($scope, 'main'));
			$scope.hideGWFContent();
			SidebarSrvc.refreshSidebarsFor($scope);
		}
	});
	
	$scope.refreshedSidebar = function(bar, result) {
		console.log('GWFCtrl.refreshedSidebar()', bar, result);
		$scope.data[bar+'Content'] = result.data;
		setTimeout(function() {
			RequestSrvc.fixForms($scope, bar, '.gwf-'+bar+'-content FORM');
			RequestSrvc.fixAnchors($scope, '.gwf-'+bar+'-content A');
			RequestSrvc.fixSelects($scope, '.gwf-'+bar+'-content SELECT');
		}, 1);
	};

	$scope.toggleLeftMenu = function() {
		$mdSidenav('left').toggle();
	};

	$scope.toggleRightMenu = function() {
		$mdSidenav('right').toggle();
	};
}).
controller('SelectCtrl', function($scope) {
	$scope.data = {
		selected: '',
	};
}).
controller('LoadingCtrl', function($scope) {
});
