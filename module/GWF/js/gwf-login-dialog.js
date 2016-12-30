'use strict';
var gwf = angular.module('gwf4');
gwf.service('LoginDlg', function($q, $mdDialog, ErrorSrvc, CommandSrvc) {
	
	var LoginDlg = this;
	
	LoginDlg.open = function(allowGuests) {
		console.log('LoginDlg.open()', allowGuests);
		return $q(function(resolve, reject){
			LoginDlg.show(resolve, reject, allowGuests);
		});
	};

	LoginDlg.show = function(resolve, reject, allowGuests) {
		function DialogController($scope, $mdDialog, user, allowGuests) {
			$scope.data = {
				user: user,
				allowGuests: allowGuests,
				username: user.JSON.user_guest_name || '',
				password: '',
			};
			$scope.closeDialog = function() {
				$mdDialog.hide();
				reject();
			};
			$scope.trialNickname = function() {
				$scope.success();
			};
			$scope.success = function() {
				$mdDialog.hide();
				resolve($scope.data.username);
			};
			
		}
		var parentEl = angular.element(document.body);
		$mdDialog.show({
			templateUrl: GWF_WEB_ROOT+'module/GWF/js/tpl/login_dlg.html',
			locals: {
				user: GWF_USER,
				allowGuests: allowGuests,
			},
			controller: DialogController
		});
	};
});
