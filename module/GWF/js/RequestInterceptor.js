'use strict';
angular.module('gwf4').
factory('RequestInterceptor', function($q, $injector) {
	var ErrorSrvc;
	return {
		'request': function(config) {
			  return config;
		},
		'requestError': function(rejection) {
	        if (!ErrorSrvc) { ErrorSrvc = $injector.get('ErrorSrvc'); }
			ErrorSrvc.showNetworkError(rejection);
			return $q.reject(rejection);
		},
		'response': function(response) {
			return response;
		},
		'responseError': function(rejection) {
			var code = rejection.status;
			if ((code == 403) || (code == 405)) {
			}
			else {
		        if (!ErrorSrvc) { ErrorSrvc = $injector.get('ErrorSrvc'); }
				ErrorSrvc.showServerError(rejection);
			}
			return $q.reject(rejection);
		}
	};
}).
config(function($httpProvider) {  
	$httpProvider.interceptors.push('RequestInterceptor');
});
