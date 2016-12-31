<?php
$headers = array(
	array($lang->lang('th_id'), 'cat_id', 'ASC'),
	array($lang->lang('th_group'), 'cat_group', 'ASC'),
	array($lang->lang('th_key'), 'cat_name', 'ASC'),
	array($lang->lang('th_edit')),
);
echo GWF_Table::start();
echo GWF_Table::displayHeaders2($headers, $tVars['sort_url']);
foreach ($tVars['cats'] as $id => $cat)
{
	echo GWF_Table::rowStart();
	echo GWF_Table::column($cat->getID());
	echo GWF_Table::column($cat->getGroup());
	echo GWF_Table::column($cat->getKey());
	echo GWF_Table::column(sprintf('<a href="%s">%s</a>', $cat->getEditHREF(), $lang->lang('btn_edit')));
	echo GWF_Table::rowEnd();
}
echo GWF_Table::end();
?>

<div><a href="<?php echo $tVars['url_new']; ?>"><?php echo $lang->lang('btn_new'); ?></a></div>
