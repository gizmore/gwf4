<?php
class GWF_FormsCombobox extends GWF_FormsString
{
	protected $options;

	public function __construct($name, $label, $value, $options, $minLength=null, $maxLength=null)
	{
		$this->options = $options;
		parent::__construct($name, $label, $value, $minLength, $maxLength);
	}

	public function renderInput()
	{
		return GWF_Select::display($this->getName(), $this->options, $this->getValue(), '', $this->getLabel(), 'form-control gwf4-combobox');
	}

	public function validate($name)
	{
		if ($error = parent::validate($name))
		{
			return $error;
		}
		$value = Common::getRequestString($name);
		if (!isset($this->options[$value]))
		{
			return GWF_HTML::err('err_invalid_option', array($this->errorFieldName()));
		}
	}

}
