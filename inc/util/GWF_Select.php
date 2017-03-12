<?php
final class GWF_Select
{
	const DEFAULT_CLASS = 'form-control';
	
	public static function display($name, $data, $selected='0', $onchange='', $label='', $class=self::DEFAULT_CLASS, $enabled=true)
	{
		$tVars = array(
			'name' => $name,
			'data' => $data,
			'selected' => $selected,
			'onchange' => $onchange,
			'selectedValue' => $selected,
			'label' => $label,
			'class' => $class,
			'enabled' => $enabled,
			'angularKeys' => GWF_Javascript::htmlAttributeEscapedJSON(array_keys($data)),
			'angularValues' => GWF_Javascript::htmlAttributeEscapedJSON(array_values($data)),
		);
		return GWF_Template::templatePHPMain('form_select.php', $tVars);
	}

	public static function multi($name, $data, $selected=array(), $onchange='')
	{
		$onchange = $onchange === '' ? '' : " onchange=\"{$onchange}\"";
		$back = '<select name="'.$name.'[]" multiple="multiple"'.$onchange.'>'.PHP_EOL;

		foreach ($data as $d)
		{
			$sel = in_array($d[0], $selected, false) ? ' selected="selected"' : '';
			$back .= sprintf('<option value="%s"%s>%s</option>', htmlspecialchars($d[0]), $sel, htmlspecialchars($d[1])).PHP_EOL;
		}
		$back .= '</select>'.PHP_EOL;
		return $back;
	}
}
