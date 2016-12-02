/**
 * http://stackoverflow.com/a/17364716
 */
angular.module('gwf4').directive('ngCrsrup', function() {
	return function(scope, element, attrs) {
		element.bind("keydown keypress", function(event) {
			console.log(event.which);
			if(event.which === 13) {
				scope.$apply(function(){
					scope.$eval(attrs.ngCrsrup, {'event': event});
				});
				event.preventDefault();
			}
		});
	};
});
