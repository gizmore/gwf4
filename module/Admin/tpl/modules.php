<?php
# Quick install all box info.
echo GWF_Box::box($install_all);

# Table heder
$headers = array(
array($lang->lang('th_priority'), 'module_priority', 'ASC'),
// array($lang->lang('th_move')),
array($lang->lang('th_version_db')),
array($lang->lang('th_name'), 'module_name', 'ASC'),
// array($lang->lang('th_version_hd')),
// array($lang->lang('th_install')),
array($lang->lang('th_basic')),
array($lang->lang('th_adv')),
);

# Btn labels
$install = $lang->lang('btn_install');
$configure = $lang->lang('btn_config');
$adminsect = $lang->lang('btn_admin_section');

# Table data
echo GWF_Table::start();
echo GWF_Table::displayHeaders1($headers, $sortUrl);
foreach ($modules as $name => $mod)
{
	$empty = GWF_Table::column();
	$enabledClass = ($mod['vdb'] == 0) || ($mod['enabled'] == 0) ? 'module-disabled' : 'module-enabled';
	echo GWF_Table::rowStart();
	echo GWF_Table::column($mod['priority']);
// 	echo $empty;
	echo GWF_Table::column($mod['vdb'], 'gwf-num '.$enabledClass);
	echo GWF_Table::column($mod['name'], 'gwf-name '.$enabledClass);
// 	echo GWF_Table::column($mod['vfs'], 'gwf-num');
	echo GWF_Table::column(sprintf('<a href="%s">%s</a>', $mod['edit_url'], $configure));
// 	echo GWF_Table::column(sprintf('<a href="%s">%s</a>', $mod['install_url'], $install));
	echo $mod['admin_url'] !== '#' ? GWF_Table::column(sprintf('<a href="%s">%s</a>', $mod['admin_url'], $adminsect)) : $empty;
	echo GWF_Table::rowEnd();
}
echo GWF_Table::end();
