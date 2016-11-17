<thead>
<?php echo $raw; ?>
<tr>
<?php echo foreach $headers as $head; ?>
	<th class="nowrap">
	<?php echo if $head[1] !== false; ?>
		<a rel="nofollow" class="gwf_th_asc<?php echo if $head[3]===true; ?>_sel<?php echo /if; ?>" href="<?php echo $head[1]; ?>"></a>
		<a rel="nofollow" class="gwf_th_desc<?php echo if $head[4]===true; ?>_sel<?php echo /if; ?>" href="<?php echo $head[2]; ?>"></a>
	<?php echo /if; ?>
	</th>
<?php echo /foreach; ?>
</tr>
<tr>
<?php echo foreach $headers as $head; ?>
	<th class="gwf_th<?php echo if isset($head[5]) && $head[5] === true; ?>_sel<?php echo /if; ?>"><?php echo $head[0]; ?></th>
<?php echo /foreach; ?>
</tr>
</thead>
