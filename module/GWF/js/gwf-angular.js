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
//	PingSrvc.ping();
}).
controller('GWFCtrl', function($scope) {
	
}).
controller('LoadingCtrl', function($scope) {
	$scope.data = {
			
	};
});
