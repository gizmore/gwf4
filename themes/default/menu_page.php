<div class="gwf_pagemenu">
<span>
<?php echo foreach $pagelinks as $id => $link; ?>
<?php echo if $link === false; ?>
	...
<?php echo elseif $link === ''; ?>
	<a class="gwf_pagemenu_sel" <?php echo $link; ?>>[<?php echo $id; ?>]</a>
<?php echo else; ?>
	<a <?php echo $link; ?>>[<?php echo $id; ?>]</a>
<?php echo /if; ?>
<?php echo /foreach; ?>
</span>
</div>
