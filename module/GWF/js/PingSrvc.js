'use strict';
angular.module('gwf4').
service('PingSrvc', function($state, RequestSrvc) {

	var PingSrvc = this;
	
	PingSrvc.RUNNING = false;
	
	PingSrvc.ping = function() {
		console.log('PingSrvc.ping()');
		if (!PingSrvc.RUNNING) {
			PingSrvc.RUNNING = true;
			ConstSrvc.inLogin(true);
			return RequestSrvc.send('gwf4/ping').then(function(data) {
//				$state.go('connect');
				PingSrvc.RUNNING = false;
			}, function(response) {
				var code = response.status;
				if ((code == 403) || (code == 405)) {
//					$state.go('login');
				}
				PingSrvc.RUNNING = false;
			});
		}
	};

});
