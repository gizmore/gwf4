'use strict';
angular.module('gwf4')
.factory('$exceptionHandler', function($injector) {
	return function(exception) {
		var ErrorSrvc = $injector.get('ErrorSrvc');
		ErrorSrvc.showException(exception);
	};
})
.service('ErrorSrvc', function($q, $mdDialog, ExceptionSrvc) {
	
	var ErrorSrvc = this;

//	ErrorSrvc.showGWFMessage = function(message) {
//		console.log(title, text);
//		console.error(text);
//		if (message.success()) {
//		}
//		else {
//		}
//	};

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
					.textContent(text)
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

	ErrorSrvc.showException = function(exception) {
		console.error(exception);
		ErrorSrvc.showError(ErrorSrvc.exceptionMessage(exception), 'Javascript error');
		ExceptionSrvc.sendReport(exception);
	};
	ErrorSrvc.exceptionMessage = function(exception) {
		return '<pre>' + exception.stack + '</pre>';
	};
		
	// --- Handler --- //
	window.onerror = function(message, filename, lineno, colno, error) {
		console.error(message, filename, lineno, colno, error);
		ErrorSrvc.showError(message, 'Javascript error');
	};

	return ErrorSrvc;
});
