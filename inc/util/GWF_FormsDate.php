<?php
class GWF_FormsDate extends GWF_FormsField
{
	private $format;
	
	public function __construct($name, $label, $value, $format='yyyymmdd', $min=null, $max=null)
	{
		$this->setName($name);
		$this->setLabel($label);
		$this->setValue($value);
		$this->format = $format;
		$this->setValidator(array($this, 'validate'));
	}
	
	public function renderInput()
	{
		return sprintf('<input name="%1$s" type="text" value="%2$s" placeholder="%3$s" format="%3$s" class="gwf4-datepicker form-control">', 
				$this->getName(), $this->getValue(), $this->format);
	}

	public function validate($name)
	{
		$value = Common::getRequestString($name);
		$value = preg_replace('/[^0-9]/', '', $value);
		$this->setValue($value);
	}
}
