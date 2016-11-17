'use strict';
angular.module('gwf4').
service('RequestSrvc', function($http) {
	
	var RequestSrvc = this;

	RequestSrvc.requestDefaultPage = function() {
		console.log('RequestSrvc.requestDefaultPage()', window.GWF_CONFIG);
		var config = window.GWF_CONFIG;
		return RequestSrvc.requestPage(config.DEFAULT_MO, config.DEFAULT_ME);
	};

	RequestSrvc.requestPage = function(module, method, data) {
		console.log('RequestSrvc.requestPage()', module, method, data);
		return RequestSrvc.send(RequestSrvc.requestURL(module, method), data);
	};
	
	RequestSrvc.requestURL = function(module, method) {
		return sprintf('%sindex.php?mo=%s&me=%s&ajax=1&gwf4am=1', GWF_CONFIG.WEB_ROOT, module, method);
	};

	RequestSrvc.send = function(url, data) {
		console.log('RequestSrvc.send()', url, data);
		return $http({
			method: 'POST',
			url: url,
			data: data,
			withCredentials: true,
			headers: {'Content-Type': 'application/x-www-form-urlencoded'},
			transformRequest: function(obj) {
				var str = [];
				for(var p in obj) {
					str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
				}
				return str.join("&");
			},
		});
	};
	
	RequestSrvc.ajaxURL = function(url) {
		url = url.substrUntil('#') || url;
		if (url.indexOf('?') === -1) {
			return url + '?ajax=1&gwf4am=1';
		}
		else {
			return url + '&ajax=1&gwf4am=1';
		}
	};

	RequestSrvc.fixAnchors = function($scope, selector) {
		console.log('RequestSrvc.fixAnchors()', selector);
		jQuery(selector).each(function(index){
			var a = $(this);
			if (a.attr('href').startsWith(GWF_CONFIG.WEB_ROOT)) {
				a.click($scope.requestPage.bind($scope, RequestSrvc.ajaxURL(a.attr('href'))));
			}
		});
	};
	
	RequestSrvc.fixForms = function($scope, selector) {
		console.log('RequestSrvc.fixForms()', selector);
		jQuery(selector).each(function(index){
			var form = $(this);
			form.submit(function(event) {
				event.preventDefault();
				RequestSrvc.sendForm(event, jQuery(this));
				return false;
			});
		});
	};

	RequestSrvc.sendForm = function(event, form) {
		console.log('RequestSrvc.sendForm()', event, form);
		RequestSrvc.send(RequestSrvc.formAction(form), RequestSrvc.formData(form));
	};

	RequestSrvc.formAction = function(form) {
		var action = form.attr('action');
		action += action.indexOf('?') > 0 ? '&' : '?';
		return action + 'ajax=1&gwf4am=1';
	};

	RequestSrvc.formData = function(form) {
		return form.serializeObject();
	};

});
