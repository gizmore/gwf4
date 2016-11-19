<?php echo GWF_Box::box($install_all); ?>
<md-list>
	<?php echo $tablehead; ?>
		<?php foreach ($modules as $name => $mod) { ?>
		<?php $enabledClass = ($mod['vdb'] == 0) || ($mod['enabled'] == 0) ? '' : ''; ?>
		<md-list-item>
			<?php echo GWF_Table::column($mod['priority']); ?>
			<?php echo GWF_Table::column(); ?>
			<?php echo GWF_Table::column($mod['name'], $enabledClass); ?>
			<?php echo GWF_Table::column($mod['vdb'], $enabledClass); ?>
			<?php echo GWF_Table::column($mod['vfs']); ?>
			<?php echo GWF_Table::column(sprintf('<a href="%s">%s</a>', $mod['edit_url'], $configure)); ?>
			<?php echo GWF_Table::column(sprintf('<a href="%s">%s</a>', $mod['install_url'], $install)); ?>
			<?php echo GWF_Table::column(sprintf('<a href="%s">%s</a>', $mod['admin_url'], $adminsect)); ?>
		</md-list-item>
	<?php } ?>
</md-list>
