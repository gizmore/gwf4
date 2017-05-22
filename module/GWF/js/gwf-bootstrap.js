/**
 * Init jquery components via classes
 */
$(function(){
	$('.gwf4-combobox').combobox();
	$('.gwf4-datepicker').each(function(){
		var jq = $(this);
		jq.datepicker({
			format: jq.attr('format'),
		});
	});
});
