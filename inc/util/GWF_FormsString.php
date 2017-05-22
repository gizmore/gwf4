<?php
class GWF_FormsString extends GWF_FormsField
{
	protected $minLength;
	protected $maxLength;

	public function __construct($name, $label, $value='', $minLength = null, $maxLength = null)
	{
		$this->setName($name);
		$this->setLabel($label);
		$this->setValue($value);
		$this->minLength = $minLength;
		$this->maxLength = $maxLength;
		$this->setValidator(array($this, 'validate'));
	}

	public function validate($name)
	{
		$value = Common::getRequestString($name);
		if ($this->minLength && (mb_strlen($value) < $this->minLength))
		{
			return GWF_HTML::err('err_str_too_short', array($this->errorFieldName(), $this->minLength));
		}
		if ($this->maxLength && (mb_strlen($value) > $this->maxLength))
		{
			return GWF_HTML::err('err_str_too_long', array($this->errorFieldName(), $this->maxLength));
		}
		$this->setValue($value);
	}

	public function renderInput()
	{
		return sprintf('<input type="text" name="%s" value="%s" class="form-control">', $this->getName(), htmlspecialchars($this->getValue()));
	}
}
