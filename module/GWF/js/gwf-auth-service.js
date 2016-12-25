'use strict';
angular.module('gwf4').
service('AuthSrvc', function($q, ErrorSrvc, LoginDlg) {
	
	var AuthSrvc = this;
	
	//////////////////
	// Cookie Check //
	//////////////////
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
	
	////////////////////
	// Nickname Check //
	////////////////////
	AuthSrvc.withNickname = function(allowGuest) {
		console.log('AuthSrvc.withNickname()');
		allowGuest = allowGuest !== false; // default true
		var user = GWF_USER; // Own GWF User
		var defer = $q.defer(); // promise
		if (user.authenticated()) {
			defer.resolve(user.name());
		}
		else if (!allowGuest) {
			defer.reject('NO GUESTS');
		}
		else if (user.hasGuestName()) {
			defer.resolve();
		}
		else { // Let user auth first
			LoginDlg.open().then(function(nickname){
				defer.resolve(nickname);
			}, function(error) {
				defer.reject(error);
			});
		}
		return defer.promise;
	};

	return AuthSrvc;
});
