'use strict';
angular.module('gwf4').
service('PingSrvc', function($state, RequestSrvc) {

	var PingSrvc = this;
	
	PingSrvc.ping = function() {
		console.log('PingSrvc.ping()');
		return RequestSrvc.requestDefaultPage();
	};

});
