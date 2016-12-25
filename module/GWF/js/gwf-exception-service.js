'use strict';
angular.module('gwf4')
.service('ExceptionSrvc', function(RequestSrvc) {
	
	var ExceptionSrvc = this;
	
	ExceptionSrvc.sendReport = function(exception) {
		console.log('ExceptionSrvc.sendReport()');
		var url = GWF_WEB_ROOT+'index.php?mo=GWF&me=AngularException&ajax=1';
		var data = ExceptionSrvc.reportData(exception);
		return RequestSrvc.send(url, data);
	};
	
	ExceptionSrvc.reportData = function(exception) {
		return {
			user_id: GWF_USER.id(),
			user_name: GWF_USER.displayName(),
			user_agent: navigator.userAgent || 0,
			resolution: sprintf('%dx%d', $(window).width(), $(window).height()),
			stacktrace: exception.stack,
		};
	};

	return ExceptionSrvc;

});
