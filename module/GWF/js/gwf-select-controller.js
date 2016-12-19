'use strict';
angular.module('gwf4').
controller('SelectCtrl', function($scope) {
	
	$scope.data = {
		items: {},
		selected: null,
	};
	
	$scope.initSelectData = function(items, selected) {
		console.log('SelectCtrl.initSelectData()', items, selected);
		$scope.data.items = items;
		$scope.data.selected = selected;
	};

});
