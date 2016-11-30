'use strict';
angular.module('gwf4').
service('VibratorSrvc', function() {

	var VibratorSrvc = this;
	
	VibratorSrvc.vibrate = function(milliseconds) {
		console.log('VibratorSrvc.vibrate()', milliseconds);
		navigator.vibrate(milliseconds);
	};
	
	VibratorSrvc.stop = function() {
		console.log('VibratorSrvc.stop()');
		navigator.vibrate(0);
	};
	
	return VibratorSrvc;

	// enable vibration support
	navigator.vibrate = navigator.vibrate || navigator.webkitVibrate || navigator.mozVibrate || navigator.msVibrate || function(ms) { console.error('vibrate not supported.'); };

});
