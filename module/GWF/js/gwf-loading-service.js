'use strict';
angular.module('gwf4').
service('LoadingSrvc', function($q, $mdDialog) {
	
	var LoadingSrvc = this;
	
	LoadingSrvc.TASKS = {};
	
	LoadingSrvc.addTask = function(task) {
//		console.log('LoadingSrvc.addTask()', task);
		LoadingSrvc.TASKS[task] = LoadingSrvc.TASKS[task] || 0;
		LoadingSrvc.TASKS[task] += 1;
	};
	
	LoadingSrvc.removeTask = function(task) {
//		console.log('LoadingSrvc.removeTask()', task);
		LoadingSrvc.TASKS[task] = LoadingSrvc.TASKS[task] || 0;
		LoadingSrvc.TASKS[task] -= 1;
		if (LoadingSrvc.TASKS[task] < 0) {
			LoadingSrvc.TASKS[task] = 0;
		}
	};
	
	LoadingSrvc.stopTask = function(task) {
//		console.log('LoadingSrvc.stopTask()', task);
		LoadingSrvc.TASKS[task] = LoadingSrvc.TASKS[task] || 0;
		LoadingSrvc.TASKS[task] = 0;
	};
	
	LoadingSrvc.stopTasks = function() {
//		console.log('LoadingSrvc.stopTasks()');
		LoadingSrvc.TASKS = {};
	};
	
	LoadingSrvc.countTasks = function() {
		var count = 0;
		var tasks = LoadingSrvc.TASKS;
		for (var task in tasks) {
			if (tasks.hasOwnProperty(task)) {
				count += tasks[task];
			}
		}
		return count;
	};
	
	LoadingSrvc.isLoading = function() {
		return LoadingSrvc.countTasks() > 0;
	};

	return LoadingSrvc;
});
