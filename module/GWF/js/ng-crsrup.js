/**
 * http://stackoverflow.com/a/17364716
 */
angular.module('gwf4').directive('ngCrsrup', function() {
	return function(scope, element, attrs) {
		element.bind("keydown keypress", function(event) {
			if(event.which === 38) {
				scope.$apply(function(){
					scope.$eval(attrs.ngCrsrup, {'event': event});
				});
				event.preventDefault();
			}
		});
	};
});
