<?php
/**
 * Base class for form fields.
 * @author gizmore
 */
abstract class GWF_FormsField
{
	private $error;

	/**
	 * @var GWF_Forms
	 */
	private $form;
	
	private $name;
	private $label;
	private $value;
	private $tooltip;
	private $validator;
	
	public function __construct($name, $label=null, $value=null)
	{
		$this->name = $name;
		$this->label = $label;
		$this->value = $value;
		$this->validator = array($this, 'validate');
	}

	public function getError() { return $this->error; }
	public function setError($error) { $this->error = $error; }
	
	public function setForm(GWF_Forms $form) { $this->form = $form; }
	public function getForm() { return $this->form; }
	public function getAction() { return $this->form->getAction(); }

	public function getName() { return $this->name; }
	public function setName($name) { $this->name = $name; }
	public function getLabel() { return $this->label; }
	public function setLabel($label) { $this->label = $label; }
	public function getValue() { return $this->value; }
	public function setValue($value) { $this->value = $value; }
	public function getTooltip() { return $this->tooltip; }
	public function setTooltip($tooltip) { $this->tooltip = $tooltip; }
	public function getValidator() { return $this->validator; }
	public function setValidator($validator) { $this->validator = $validator; }
	
	public function displayError() { return htmlspecialchars($this->getError()); }
	public function displayLabel() { return htmlspecialchars($this->getLabel()); }
	public function displayValue() { return htmlspecialchars($this->getValue()); }
	
	public function onFlowUpload() {}
	public function onCleanup() {}
	
	public function errorFieldName()
	{
		$label = $this->getLabel();
		return empty($label) ? $this->getName() : $label;
	}

	public function renderInput()
	{
		return $this->hiddenInput();
	}

	public function hiddenInput()
	{
		return sprintf('<input type="hidden" name="%s" value="%s">', $this->getName(), htmlspecialchars($this->getValue()));
	}

	public function renderLabel()
	{
		return sprintf('<label for="%s">%s</label>', $this->getName(), htmlspecialchars($this->label));
	}
	
	public function render()
	{
		return sprintf('<div class="col-md-3">%s</div><div class="col-md-9">%s</div>', $this->renderLabel(), $this->renderInput());
	}
	
	public function validate($name)
	{
		$this->value = Common::getRequestString($name);
	}

	public function getFormValue()
	{
		return $this->value;
	}
}
