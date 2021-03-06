'use strict';
angular.module('gwf4').
service('RequestSrvc', function($http, LoadingSrvc) {
	
	var RequestSrvc = this;

	//////////////////
	// Send request //
	//////////////////
//	RequestSrvc.requestDefaultPage = function() {
//		console.log('RequestSrvc.requestDefaultPage()', window.GWF_CONFIG);
//		var config = window.GWF_CONFIG;
//		return RequestSrvc.requestPage(config.DEFAULT_MO, config.DEFAULT_ME);
//	};
//	RequestSrvc.requestPage = function(module, method, data) {
//		console.log('RequestSrvc.requestPage()', module, method, data);
//		return RequestSrvc.send(RequestSrvc.requestURL(module, method), data);
//	};
//	
//	RequestSrvc.requestURL = function(module, method) {
//		return sprintf('%sindex.php?mo=%s&me=%s&ajax=1&gwf4am=1', GWF_CONFIG.WEB_ROOT, module, method);
//	};

	RequestSrvc.send = function(url, data, contentType) {
		console.log('RequestSrvc.send()', url, data, contentType);
		var contentType = contentType ? contentType : 'application/x-www-form-urlencoded'; // 'multipart/form-data; charset=utf-8'
		contentType = 'application/x-www-form-urlencoded'; // 'multipart/form-data; charset=utf-8'
		var isFile = contentType.indexOf('multipart/') === 0;
//		contentType = isFile ? undefined : contentType;
		var headers = {
			'Content-Type': contentType	
		};
		LoadingSrvc.addTask('http');
//		var transform = isFile ? angular.identity : RequestSrvc.transformPostData;
		return $http({
			method: 'POST',
			url: url,
			data: data,
			withCredentials: true,
			headers: headers,
			transformRequest: RequestSrvc.transformPostData
		})['finally'](function() {
			LoadingSrvc.removeTask('http');
		});
	};
	
	RequestSrvc.transformPostData = function(obj) {
		var str = [];
		for(var p in obj) {
			str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
		}
		return str.join("&");
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

	///////////////
	// Send form //
	///////////////
	RequestSrvc.sendForm = function(event, form) {
		console.log('RequestSrvc.sendForm()', event, form);
		return RequestSrvc.send(RequestSrvc.formAction(form), RequestSrvc.formData(form), RequestSrvc.formEncoding(form));
	};

	RequestSrvc.formEncoding = function(form) {
		console.log('RequestSrvc.formEncoding()', form);
		return form.attr('enctype');
	};
	
	RequestSrvc.formAction = function(form) {
		var action = form.attr('action');
		action += action.indexOf('?') > 0 ? '&' : '?';
		return action + 'ajax=1&gwf4am=1';
	};

	RequestSrvc.formData = function(form) {
		// Checkboxes
        var data = form.serializeObject();
        form.find('md-checkbox.md-checked').each(function(index){
        	var cbx = jQuery(this);
        	data[cbx.attr('name')] = true;
        });
        // Submit button
        var input = jQuery("input[type=submit][clicked=true]");
        var key = input.attr('name');
        if (key) {
        	var val = input.val();
        	data[key] = val;
        }

        // Image button
        input = jQuery("input[type=image][clicked=true]");
        key = input.attr('name');
        if (key) {
        	data[key] = key;
        }

        // Done
        return data;
	};

	////////////////////////////
	// Hook anchors and forms //
	////////////////////////////
	RequestSrvc.fixAnchors = function($scope, selector) {
//		console.log('RequestSrvc.fixAnchors()', selector);
		jQuery(selector).each(function(index){
			var a = $(this);
			var href = a.attr('href');
			if (href && href.startsWith(GWF_CONFIG.WEB_ROOT)) {
				if (!a.attr('gwf-hooked')) {
					a.attr('gwf-hooked', '1');
					a.click($scope.requestPage.bind($scope, RequestSrvc.ajaxURL(href)));
				}
			}
		});
	};
	
	RequestSrvc.fixForms = function($scope, area, selector) {
//		console.log('RequestSrvc.fixForms()', area, selector);
		jQuery(selector).each(function(index){
			var form = $(this);
			if (!form.attr('gwf-hooked')) {
				form.attr('gwf-hooked', '1');
				if (form.attr('action').startsWith(GWF_WEB_ROOT)) {
					selector = sprintf('%1$s input[type=submit], %1$s input[type=image]', selector);
					jQuery(selector).click(function() {
						jQuery("input[type=submit][clicked=true], input[type=image][clicked=true]").removeAttr("clicked");
						jQuery(this).attr("clicked", "true");
				    });
					form.submit(function(event) {
						event.preventDefault();
						var f = jQuery(this);
						if (!f.attr('gwf-sent')) {
							f.attr('gwf-sent', '1');
							$scope.hideGWFContent();
							RequestSrvc.sendForm(event, jQuery(this)).then(function(result){
								$scope.formRequested(area, result);
							}); 
						}
						return false;
					});
				}
			};
		});
	};

	RequestSrvc.fixSelects = function($scope, selector) {
//		console.log('RequestSrvc.fixSelects()', selector);
	};
	
});
