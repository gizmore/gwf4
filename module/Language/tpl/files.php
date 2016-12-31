<?php echo GWF_Button::generic($lang->lang('btn_checker'), $tVars['href_checker']); ?>
<?php echo GWF_Button::generic($lang->lang('btn_bundle'), $tVars['href_bundle']); ?>

<?php
$headers = array(
	array($lang->lang('th_filename')),
	array($lang->lang('th_count')),
	array($lang->lang('th_branched')),
	array($lang->lang('th_filesize')),
);

$counter = array();
$files = $tVars['files'];

foreach ($files as $file)
{
	list($fullpath, $branched, $langfile, $iso, $filename) = $file;
	if (!isset($counter[$fullpath])) {
		$counter[$fullpath] = 1;
	} else {
		$counter[$fullpath]++;
	}
}

$yes = $lang->lang('yes');
$no = $lang->lang('no');

echo '<table>';
echo GWF_Table::displayHeaders1($headers, '');
foreach ($files as $file)
{
	list($fullpath, $branched, $langfile, $iso, $filename) = $file;
	if ($iso !== 'en') {
		continue;
	}
	$href = GWF_WEB_ROOT.'index.php?mo=Language&amp;me=EditFiles&amp;filename='.urlencode($fullpath);
	$count = $counter[$fullpath];
	echo GWF_Table::rowStart();
	echo sprintf('<td><a href="%s">%s</a></td>', $href, GWF_HTML::display($fullpath));
	echo sprintf('<td><a href="%s">%d</a></td>', $href, $count);
	echo sprintf('<td><a href="%s">%s</a></td>', $href, $branched ? $yes : $no);
	echo sprintf('<td><a href="%s">%s</a></td>', $href, $langfile->getVar('lf_size'));
	echo GWF_Table::rowEnd();
}
echo '</table>';

?>