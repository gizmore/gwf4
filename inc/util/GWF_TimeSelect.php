<?php
/**
 * A double select for hh:ii
 * @author gizmore
 */
final class GWF_TimeSelect
{
	public static function selectNow($name1='hour', $name2='min', $selected=true)
	{
		return self::select($name1, $name2, $selected);
	}
	
	public static function select($name1='hour', $name2='min', $selected='0000')
	{
		$tVars = array(
				'nameHour' => $name1,
				'nameMinute' => $name2,
				'selected' => $selected,
		);
		return GWF_Template::templatePHPMain('select_time.php', $tVars);
	}

	public static function isValidTime($arg, $allow_zero)
	{
		if (strlen($arg) !== 4)
		{
			return false;
		}

		$h = substr($arg, 0, 2);
		if ($h < 0 || $h > 23)
		{
			return false;
		}

		$i = substr($arg, 2, 2);
		if ($i < 0 || $i > 59)
		{
			return false;
		}

		return true;
	}
}
