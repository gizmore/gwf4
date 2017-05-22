<?php
$onchange = $onchange === '' ? '' : " onchange=\"{$onchange}\"";
echo '<select class="'.$class.'" name="'.$name.'"'.$onchange.'>'.PHP_EOL;
foreach ($data as $value => $label)
{
	$sel = $value == $selected ? ' selected="selected"' : '';
	printf('<option value="%s"%s>%s</option>', htmlspecialchars($value), $sel, htmlspecialchars($label)).PHP_EOL;
}
echo '</select>'.PHP_EOL;
