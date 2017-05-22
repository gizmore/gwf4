<?php
if ($selected === true)
{
	$selected = sprintf('%02d%02d', Common::getPostInt($name1, date('H')), Common::getPostInt($name2, date('i')));
}
else
{
	$selected = sprintf('%04d', $selected);
}

$data = array();
for ($i = 0; $i < 24; $i++)
{
	$data[$i] = $i;
}
$sel1 = GWF_Select::display($nameHour, $data, substr($selected, 0, 2), '', '', '');

$data = array();
for ($i = 0; $i < 60; $i++)
{
	$data[$i] = $i;
}
$sel2 = GWF_Select::display($nameMinute, $data, substr($selected, 2, 2), '', '', '');

echo $sel1.':'.$sel2;
