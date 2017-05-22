<div class="gwf_letter_menu_outer">
<div class="gwf_letter_menu">
<?php echo foreach $letters as $letter => $href; ?>
	<a<?php echo if $letter === $selected; ?> class="sel"<?php echo /if; ?><?php echo $href; ?>><?php echo $letter; ?></a>

<?php echo * gizmore TODO: remove if not needet
	{GWF_HTML::anchor($href, $letter, $sel); ?>
	<?php echo assign var='sel' value="{GWF_HTML::selected($letter === $selected); ?>"}
	<a<?php echo $href; ?><?php echo $sel; ?>><?php echo $letter; ?></a>
*}
<?php echo /foreach; ?>
</div>
</div>
