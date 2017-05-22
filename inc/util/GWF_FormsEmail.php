<?php
class GWF_FormsEmail extends GWF_FormsString
{
	public function __construct($name, $label, $value, $minLength = null, $maxLength = null)
	{
		parent::__construct($name, $label, $value, $minLength, $maxLength);
	}

	public function renderInput()
	{
		return sprintf('<input type="email" name="%s" value="%s" class="form-control">', $this->getName(), htmlspecialchars($this->getValue()));
	}
}
