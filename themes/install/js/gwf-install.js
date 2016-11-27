function installToggleAllModules(checked) {
	$('input:checkbox').each(function(index) {
		$this = $(this);
		if ($this.attr('disabled') !== 'disabled') {
			if (checked) {
				$this.attr('checked', 'checked');
			}
			else {
				$this.removeAttr('checked');
			}
		}
	});
}
