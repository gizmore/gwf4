'use strict';
angular.module('gwf4').
service('RequestSrvc', function($http) {
	
	var RequestSrvc = this;
	
	RequestSrvc.send = function(method, data) {
		return $http({
			method: 'POST',
			url: method,
			data: data,
			withCredentials: true,
			headers: {'Content-Type': 'application/x-www-form-urlencoded'},
			transformRequest: function(obj) {
				var str = [];
				for(var p in obj) 
					str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
				return str.join("&");
			},
		});
	};

});
