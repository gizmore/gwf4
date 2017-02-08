'use strict';
angular.module('gwf4', ['ngMaterial', 'ngSanitize', 'ui.router', 'flow', 'monospaced.mousewheel']).
config(function($urlRouterProvider, $stateProvider) {
	$stateProvider.state({
		name: 'loading',
		url: '/loading',
		controller: 'GWFCtrl',
		templateUrl: GWF_WEB_ROOT+'module/GWF/js/tpl/loading.html',
		pageTitle: 'Loading'
	});
	$stateProvider.state({
		name: 'page',
		url: '/page/:url',
		controller: 'GWFCtrl',
		templateUrl: GWF_WEB_ROOT+'module/GWF/js/tpl/page.html',
		pageTitle: 'GWF4'
	});
	$urlRouterProvider.otherwise('/loading');
}).
run(function($injector) {
	window.INJECTOR = $injector; // Oops. Angular exposed to window.
}).
controller('GWFCtrl', function($scope, $q, $state, $mdSidenav, ErrorSrvc, AuthSrvc, RequestSrvc, SidebarSrvc, LoadingSrvc) {
	if (!window.GWF_SIDEBAR_INIT) {
		window.GWF_SIDEBAR_INIT = true;

		$scope.data = {
			user: GWF_USER,
			mainContent: '',
			topContent: '',
			leftContent: '',
			rightContent: '',
			bottomContent: '',
		};
		
		$scope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams) {
			if (!window.GWF_SIDEBAR_INITED) {
				window.GWF_SIDEBAR_INITED = true;
				$scope.initGWF();
			}
			if (toParams.url) {
				RequestSrvc.send(toParams.url).then($scope.pageRequested);
			}
		});

		$scope.initGWF = function() {
			console.log('GWFCtrl.initGWF()');
			AuthSrvc.withCookies().then($scope.initGWFSidebar);
			$scope.copyGWFPage();
		};
		
		$scope.copyGWFPage = function() {
			setTimeout(function(){
				$('#gwf-page-content').appendTo($('ui-view.gwf-angular-main-view'));
			}, 0);
		};
		
		$scope.initGWFSidebar = function() {
//			console.log('GWFCtrl.initGWFSidebar()');
			RequestSrvc.fixForms($scope, 'main', 'FORM');
			RequestSrvc.fixAnchors($scope, 'A');
			RequestSrvc.fixSelects($scope, 'SELECT');
			SidebarSrvc.refreshSidebarsFor($scope);
		};
		
		$scope.refreshSidebar = function() {
			return SidebarSrvc.refreshSidebar();
		};
		
//		$scope.requestState = function(name, params) {
//			console.log('GWFCtrl.requestState()', name, params);
//			$scope.hideGWFContent();
//			return $state.go(name, params);
//		};
		
		$scope.requestGWFPage = function(module, method, data) {
			console.log('GWFCtrl.requestGWFPage()', module, method, data);
			var url = GWF_WEB_ROOT + sprintf('index.php?mo=%s&me=%s&ajax=1');
			return $scope.requestPage(url);
		};
		
		$scope.showLoadingBackdrop = function() {
			return LoadingSrvc.isLoading();
		};

		$scope.requestPage = function(url) {
			console.log('GWFCtrl.requestPage()', url);
			$scope.hideGWFContent();
			$scope.closeSidenavs();
			return $state.go('page', {url:url});
		};

		$scope.pageRequested = function(result) {
			console.log('GWFCtrl.pageRequested()', result);
			$scope.hideGWFContent();
			$scope.data.mainContent = result.data;
			setTimeout($scope.afterRefreshContent.bind($scope, 'main'), 1);
		};
		
		$scope.closeSidenavs = function() {
//			console.log('GWFCtrl.closeSidenavs()');
			$mdSidenav('left').close();
			$mdSidenav('right').close();
		};
		
		$scope.hideGWFContent = function() {
//			console.log('GWFCtrl.hideGWFContent()');
			jQuery('#gwf-page-content').remove();
		};
		
		$scope.focusMainContent = function() {
			$scope.closeSidenavs();
			$scope.hideGWFContent();
		};

		$scope.formRequested = function(srcbar, result) {
			console.log('GWFCtrl.formRequested()', bar, result);
			var bar = 'main';
			$scope.hideGWFContent();
			$scope.closeSidenavs();
			$scope.data[bar+'Content'] = result.data;
			setTimeout($scope.afterRefreshContent.bind($scope, bar), 1);
			setTimeout(function(){
				SidebarSrvc.refreshSidebarsFor($scope);
			}, 1000);
		};
		
		$scope.refreshedSidebar = function(bar, result) {
//			console.log('GWFCtrl.refreshedSidebar()', bar, result);
			var bars = bar.split(',');
			for (var i in bars) {
				bar = bars[i];
				$scope.data[bar+'Content'] = result.data[bar];
				setTimeout($scope.afterRefreshContent.bind($scope, bar), 1);
			}
		};

		$scope.afterRefreshContent = function(bar) {
//			console.log('GWFCtrl.afterRefreshedSidabar()', bar);
			RequestSrvc.fixForms($scope, bar, '.gwf-'+bar+'-content FORM');
			RequestSrvc.fixAnchors($scope, '.gwf-'+bar+'-content A');
			RequestSrvc.fixSelects($scope, '.gwf-'+bar+'-content SELECT');
		};
		
		$scope.toggleLeftMenu = function() {
			$mdSidenav('left').toggle();
		};

		$scope.toggleRightMenu = function() {
			$mdSidenav('right').toggle();
		};
		
//		$scope.initGWF();

	}
});
