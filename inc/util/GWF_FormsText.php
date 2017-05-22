<?php
class GWF_FormsText extends GWF_FormsString
{
	public function __construct($name, $label, $value, $minLength = null, $maxLength = null)
	{
		parent::__construct($name, $label, $value, $minLength, $maxLength);
	}

	public function renderInput()
	{
		return sprintf('<textarea name="%s">%s</textarea>', $this->getName(), htmlspecialchars($this->getValue()));
	}
}
