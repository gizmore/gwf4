<?php
echo GWF_Box::box($install_all);
echo GWF_Table::start();
echo $tablehead;
foreach ($modules as $name => $mod)
{
	$empty = GWF_Table::column();
	$enabledClass = ($mod['vdb'] == 0) || ($mod['enabled'] == 0) ? 'module-disabled' : 'module-enabled';
	echo GWF_Table::rowStart();
	echo GWF_Table::column($mod['priority']);
	echo $empty;
	echo GWF_Table::column($mod['name'], 'gwf-name '.$enabledClass);
	echo GWF_Table::column($mod['vdb'], 'gwf-num '.$enabledClass);
	echo GWF_Table::column($mod['vfs'], 'gwf-num');
	echo GWF_Table::column(sprintf('<a href="%s">%s</a>', $mod['edit_url'], $configure));
	echo GWF_Table::column(sprintf('<a href="%s">%s</a>', $mod['install_url'], $install));
	echo $mod['admin_url'] !== '#' ? GWF_Table::column(sprintf('<a href="%s">%s</a>', $mod['admin_url'], $adminsect)) : $empty;
	echo GWF_Table::rowEnd();
}
echo GWF_Table::end();
