<gwf-pagemenu>
<?php foreach ($pagelinks as $id => $link)
{
	if ($link === false) {
		echo "&nbsp;…&nbsp;";
	}
	elseif ($link === '') {
		printf('<a class="selected" %s>[%s]</a>', $link, $id);
	}
	else {
		printf('<a %s>[%s]</a>', $link, $id);
	}
}
?>
</gwf-pagemenu>
