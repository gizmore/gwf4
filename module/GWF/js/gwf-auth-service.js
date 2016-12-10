'use strict';
angular.module('gwf4').
service('AuthSrvc', function($q, ErrorSrvc) {
	
	var AuthSrvc = this;
	
	AuthSrvc.withCookies = function() {
		console.log('AuthSrvc.withCookies()');
		var defer = $q.defer();
		
		if (GWF_CONFIG.HAS_COOKIES)
		{
			defer.resolve(GWF_CONFIG);
		}
		else
		{
			ErrorSrvc.showError('You have no cookies', GWF_CONFIG.SITENAME).then(AuthSrvc.refreshHotfix());
			defer.reject();
		}
		
		return defer.promise;
	};
	
	AuthSrvc.refreshHotfix = function() {
		console.log('AuthSrvc.refreshHotfix()');
	};

	return AuthSrvc;
});
