'use strict';
angular.module('gwf4').
controller('SelectCtrl', function($scope) {
	
	$scope.data = {
		keys: null,
		values: null,
		selected: null,
	};
	
	$scope.initSelectData = function(keys, values, selected) {
		console.log('SelectCtrl.initSelectData()', keys, values, selected);
		$scope.data.keys = keys;
		$scope.data.values = values;
		$scope.data.selected = selected;
	};

});
