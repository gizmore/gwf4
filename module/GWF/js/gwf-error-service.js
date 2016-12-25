'use strict';
angular.module('gwf4')
.factory('$exceptionHandler', function($injector) {
	return function(exception) {
		$injector.get('ErrorSrvc').handleException(exception);
	};
})
.service('ErrorSrvc', function($q, $mdDialog, ExceptionSrvc, LoadingSrvc) {
	
	var ErrorSrvc = this;

	// --- Dialogs --- //
	ErrorSrvc.showMessage = function(text, title) {
		return $mdDialog.show(
				$mdDialog.alert()
				.clickOutsideToClose(true)
				.title(title)
				.htmlContent(text)
				.ariaLabel(title)
				.ok("OK")
				);
	};
	
	ErrorSrvc.showError = function(text, title) {
		console.log(title, text);
		return $mdDialog.show(
					$mdDialog.alert()
//					.parent(angular.element(document.querySelector('#popupContainer')))
					.clickOutsideToClose(false)
					.title(title)
					.htmlContent(text)
					.ariaLabel(title)
					.ok("Aww")
					);
	};
	
	// --- Titles --- //
	ErrorSrvc.show404Error = function(error) {
		return ErrorSrvc.showError(error.statusText, 'Server Error');
	};
	ErrorSrvc.showNetworkError = function(error) {
		return ErrorSrvc.showError(error, 'Netz doof');
	};
	ErrorSrvc.showServerError = function(error) {
		return ErrorSrvc.showError(error, 'Server Error');
	};
	ErrorSrvc.showUserError = function(error) {
		ErrorSrvc.showError(error, "User error");
	}
	
	// --- Exceptions --- //
	window.onerror = function(message, filename, lineno, colno, error) { ErrorSrvc.handleException(new Error(message)); };
	ErrorSrvc.handleException = function(exception) {
		console.error(exception);
		ErrorSrvc.showError('<pre>'+exception.stack+'</pre>', 'Javascript error');
		ExceptionSrvc.sendReport(exception).then(LoadingSrvc.stopTasks);
	};

	return ErrorSrvc;
});
