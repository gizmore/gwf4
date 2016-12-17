'use strict';
angular.module('gwf4').
filter('transferSpeed', function() {
	return function(s) {
		return parseInt(s / 1024) + "kbps";
	};
});
