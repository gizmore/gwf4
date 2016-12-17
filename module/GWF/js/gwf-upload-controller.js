'use strict';
angular.module('gwf4').
controller('UploadCtrl', function($scope, ErrorSrvc) {
	
	$scope.data = {
		transfer: {
			fileNum: 0,
			filesCount: 0,
			bytesTotal: 0,
			bytesTransferred: 0,
			speed: '0',
			fileName: '',
			inProgress: false,
		},
	};
	
	$scope.onFlowError = function($file, $flow) {
		ErrorSrvc.showError('Flow Error', 'Flow Error');
	};

	
	$scope.onFlowProgress = function($file, $flow) {
//		console.log('$scope.onFlowProgress()', $file, $flow);
		var transfer = $scope.data.transfer;
		var j = 0, index = 0;
		transfer.bytesTotal = 0;
		transfer.bytesTransferred = 0;
		for (var i in $flow.files) {
			// Detect file num
			var file = $flow.files[i];
			if (file === $file) {
				index = j;
			}
			j++;
			// Sum bytes
			transfer.bytesTotal += $file.size;
			transfer.bytesTransferred = $file._prevUploadedSize;
		}
		transfer.fileNum = index + 1;
		transfer.filesCount = $flow.files.length;
		transfer.speed = $file.currentSpeed;
		transfer.fileName = $file.name;
		transfer.inProgress = true;
	};

	$scope.onFlowSuccess = function($file, $flow) {
		console.log('$scope.onFlowSuccess()', $file, $flow);
		$scope.data.transfer.inProgress = false;
	};
	
	$scope.progressIndicatorDisabled = function() {
		return !$scope.data.transfer.inProgress;
	};
	
	$scope.progressIndicatorValue = function() {
		var t = $scope.data.transfer;
		var value = t.bytesTransferred / t.bytesTotal;
		return value * 100;
	};

});
