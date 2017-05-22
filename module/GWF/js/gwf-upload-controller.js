'use strict';
angular.module('gwf4').
controller('UploadCtrl', function($scope, $http) {
	
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
	
	$scope.initGWFFormConfig = function(config) {
		console.log('UploadCtrl.initGWFFormConfig()', config);
		$scope.data.config = config;
	};
	
	$scope.onFlowSubmitted = function($flow) {
		console.log('UploadCtrl.onFlowSubmitted()', $flow);
		var acceptedFiles = [];
		for (var i in $flow.files) {
			if ($scope.isValidFile($flow.files[i])) {
				acceptedFiles.push($flow.files[i]);
			}
		}
		$flow.files = acceptedFiles;
		$flow.upload();
	};
	
	$scope.isValidFile = function($file) {
		console.log('UploadCtrl.isValidFile()', $file, $scope.data.config);
		var maxSize = $scope.data.config.maxSize;
		var mimeTypes = $scope.data.config.mimeTypes;
		if ($file.size > maxSize) {
			$scope.denyFile($file, 'Max size exceeded.');
		}
		else if ((mimeTypes.indexOf($file.file.type) < 0) && (mimeTypes.length > 0)) {
			$scope.denyFile($file, 'Invalid mime type.');
		}
		else {
			return true;
		}
	};
	
	$scope.denyFile = function($file, error) {
		console.log('UploadCtrl.denyFile()', $file, error);
		alert(error);
//		ErrorSrvc.showError(error, 'Upload');
	};
	
	$scope.onRemoveFile = function($file, $flow) {
		console.log('UploadCtrl.onRemoveFile()', $file);
		alert('Removing files during upload is not yet supported.');
		// TODO: Send http request to indicate to delete a temp upload?
		//       Alternatively, set hidden fields for deleted files?
		// THEN: $flow.removeFile($file);
	};

	
	$scope.onFlowError = function($file, $flow, $message) {
		console.log('UploadCtrl.onFlowError()', $file, $flow, $message);
		$scope.denyFile($file, $message);
	};
	
	$scope.onFlowProgress = function($file, $flow, $msg) {
//		console.log('UploadCtrl.onFlowProgress()', $file, $flow, $msg);
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

	$scope.onFlowSuccess = function($file, $flow, $msg) {
		console.log('UploadCtrl.onFlowSuccess()', $file, $flow, $msg);
		$scope.data.transfer.speed = $scope.data.transfer.bytesTotal;
		$scope.data.transfer.bytesTransferred = $scope.data.transfer.bytesTotal;
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
