<?php
class GWF_FormsCountrySelect extends GWF_FormsSelect
{
	private $multiple;
	
	public function __construct($name, $label, $value, $emptyLabel=true, $multiple=false)
	{
		parent::__construct($name, $label, $value, $this->getCountryOptions($emptyLabel));
		$this->multiple = $multiple;
	}
	
	public function getCountryOptions($emptyLabel)
	{
		$table = GDO::table('GWF_Country');
		$result = $table->select('country_id,country_name', '', 'country_name ASC');
		$data = array();
	
		if ($emptyLabel)
		{
			$emptyLabel = $emptyLabel === true ? GWF_HTML::lang('sel_country') : $emptyLabel;
			$data["0"] =  $emptyLabel;
		}
		while (false !== ($row = $table->fetch($result, GDO::ARRAY_N)))
		{
			$data[$row[0]] = $row[1];
		}
		$table->free($result);
		return $data;
	}
}
