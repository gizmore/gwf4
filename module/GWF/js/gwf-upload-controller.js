'use strict'
angular.module('gwf4-upload').
controller('UploadCtrl', function($scope) {
	
	$scope.data = {
		
	};
	
	$scope.onProgress = function($file, $flow) {
		console.log('$scope.onProgress()', $file, $flow);
	};
	
});
