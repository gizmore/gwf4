<?php
class GWF_FormsSubmit extends GWF_FormsField
{
	public function __construct($name, $label, $value)
	{
		parent::__construct($name, $label, $value);
	}
	
	public function renderInput()
	{
		return sprintf('<button type="submit" name="%s" class="btn btn-primary" value="%s">%s</button>',
				$this->getName(), $this->displayValue(), $this->displayLabel());
	}
	
	public function getFormValue()
	{
	}
	
}