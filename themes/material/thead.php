<thead>
<?php echo $raw; ?>
<tr>
<?php foreach ($headers as $head) { ?>
	<th class="nowrap">
	<?php
	if ($head[1] !== false)
	{
		printf('<a rel="nofollow" class="gwf_th_asc%s" href="%s"></a>', $head[3]===true ? '_sel' : '', $head[1]);
		printf('<a rel="nofollow" class="gwf_th_desc%s" href="%s"></a>', $head[4]===true ? '_sel' : '', $head[2]);
	}
	?>
	</th>
<?php } ?>
</tr>
<tr>
<?php
foreach ($headers as $head)
{
	$sel = isset($head[5]) && ($head[5] === true) ? '_sel' : '';
	printf('<th class="gwf_th%s">%s</th>', $sel, $head[0]);
}
?>
</tr>
</thead>
