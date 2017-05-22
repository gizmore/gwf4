<?php
class GWF_FormsSelect extends GWF_FormsField
{
	protected $options;

	public function __construct($name, $label, $value, $options)
	{
		$this->setName($name);
		$this->setLabel($label);
		$this->setValue($value);
		$this->options = $options;
		$this->setValidator(array($this, 'validate'));
	}

	public function renderInput()
	{
		return GWF_Select::display($this->getName(), $this->options, $this->getValue(), '', $this->getLabel());
	}

	public function validate($name)
	{
		$value = Common::getRequestString($name);
		if (!isset($this->options[$value]))
		{
			return GWF_HTML::err('err_invalid_option', array($this->errorFieldName()));
		}
		$this->setValue($value);
	}

}
