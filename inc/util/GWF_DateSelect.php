<?php
final class GWF_DateSelect
{
	public static function getDateSelects($key, $default, $size, $with_compare, $in_future, $less_selects=false)
	{
		$this_year = intval(date('Y'));
		$minyear = $in_future ? $this_year : 1900;
		$maxyear = $in_future ? $this_year+2 : $this_year; 

		$selects = array();
		switch ($size)
		{
			case GWF_Date::LEN_SECOND:
				$aKey = $key.'s';
				$def = Common::getPost($aKey, substr($default, 12, 2));
				if ($less_selects === true) {
					$selects['s'] = self::getFormInput($aKey, self::STRING, $def, 2);
				} else {
					$selects['s'] = self::getSecondInput($aKey, $def);
				}
			case GWF_Date::LEN_MINUTE:
				$aKey = $key.'i';
				$def = Common::getPost($aKey, substr($default, 10, 2));
				if ($less_selects === true) {
					$selects['i'] = self::getFormInput($aKey, self::STRING, $def, 2);
				} else {
					$selects['i'] = self::getMinuteInput($aKey, $def);
				}
			case GWF_Date::LEN_HOUR;
				$aKey = $key.'h';
				$def = Common::getPost($aKey, substr($default, 8, 2));
				if ($less_selects === true) {
					$selects['h'] = self::getFormInput($aKey, self::STRING, $def, 2);
				} else {
					$selects['h'] = self::getHourInput($aKey, $def);
				}
			case GWF_Date::LEN_DAY:
				$aKey = $key.'d';
				$def = Common::getPost($aKey, substr($default, 6, 2));
				if ($less_selects === true) {
					$selects['d'] = self::getFormInput($aKey, self::STRING, $def, 2);
				} else {
					$selects['d'] = self::getDayInput($aKey, $def);
				}
			case GWF_Date::LEN_MONTH:
				$aKey = $key.'m';
				$def = Common::getPost($aKey, substr($default, 4, 2));
				$selects['m'] = self::getMonthInput($aKey, $def);
			case GWF_Date::LEN_YEAR:
				$aKey = $key.'y';
				$def = Common::getPost($aKey, substr($default, 0, 4));
				if ($less_selects === true) {
					$selects['y'] = self::getFormInput($aKey, self::STRING, $def, 4);
				}
				else {
					$selects['y'] = self::getYearInput($aKey, $def, $minyear, $maxyear);
				}
		}

		$format = strtolower(GWF_HTML::lang('df'.$size));
		$format = str_replace(array('n','j','l'), array('m','d','d'), $format);

		$back = '';
		if ($with_compare)
		{
			$aKey = $key.'c';
			$back = self::getDateCmpInput($aKey, Common::getPost($aKey, 'younger'));
		}

		$taken = array();

		$len = strlen($format);
		for ($i = 0; $i < $len; $i++)
		{
			$c = $format{$i};
			if (isset($selects[$c]))
			{
				if (!in_array($c, $taken)) {
					$back .= $selects[$c];
					$taken[] = $c;
				}
			}
			elseif ($less_selects === true)
			{
				$back .= " $c ";
			}
			else
			{
				$back .= ' ';
			}
		}

		return $back;
	}

	private static function getDateCmpInput($key, $selected)
	{
		$data = array(
			'older' => GWF_HTML::lang('sel_older'),
			'younger' => GWF_HTML::lang('sel_younger'),
		);
		return GWF_Select::display($key, $data, $selected);
	}

	private static function getSecondInput($key, $selected) { return self::getRangeInput($key, $selected, 0, 59); }
	private static function getMinuteInput($key, $selected) { return self::getRangeInput($key, $selected, 0, 59); }
	private static function getHourInput($key, $selected) { return self::getRangeInput($key, $selected, 0, 23); }
	private static function getRangeInput($key, $selected, $min, $max)
	{
		$data = array();
		while ($min <= $max)
		{
			$s = sprintf('%02d', $min++);
			$data[$s] = $s;
		}
		return GWF_Select::display($key, $data, $selected);
	}

	private static function getDayInput($key, $selected=false)
	{
		$data = array('00' => GWF_HTML::lang('sel_day'));
		for ($i = 1; $i <= 31; $i++)
		{
			$s = sprintf('%02d', $i);
			$data[$s] = $s;
		}
		return GWF_Select::display($key, $data, $selected);
	}

	private static function getMonthInput($key, $selected=false)
	{
		$data = array('00' => GWF_HTML::lang('sel_month'));
		for ($i = 1; $i <= 12; $i++)
		{
			$s = sprintf('%02d', $i);
			$data[$s] = GWF_HTML::lang('M'.$i);
		}
		return GWF_Select::display($key, $data, $selected);
	}

	private static function getYearInput($key, $selected=false, $min=1900, $max=NULL)
	{
		$min = (int) $min;
		if (!is_numeric($selected)) { $selected = 0; }
		if (!is_numeric($max)) { $max = date('Y'); }
		
		$data = array('0000' => GWF_HTML::lang('sel_year'));
		for ($i = $max; $i >= $min; $i--)
		{
			$s = sprintf('%04d', $i);
			$data[$s] = $i;
		}
		return GWF_Select::display($key, $data, $selected);
	}	
}
