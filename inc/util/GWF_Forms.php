<?php
final class GWF_Forms
{
	const METHOD_GET = 'get';
	const METHOD_POST = 'post';
	
	const ENCODING_FORM_URL = '';
	const ENCODING_MULTIPART = '';
	
	private $fields;
	private $title;
	private $action;
	private $encoding = self::ENCODING_FORM_URL;
	private $method = self::METHOD_POST;
	
	public function getTitle() { return $this->title; }
	public function setTitle($title) { $this->title = $title; }
	public function getAction() { return $this->action; }
	public function setAction($action) { $this->action = $action; }
	public function getEncoding() { return $this->encoding; }
	public function setEncoding($encoding) { $this->encoding = $encoding; }
	public function getMethod() { return $this->method; }
	public function setMethod($method) { $this->method = $method; }
	
	public function getVar($field) { return $this->field($field)->getValue(); }
	
	public function __construct()
	{
		$this->fields = array();
	}
	
	public function onFileUpload()
	{
		if (isset($_REQUEST['flowIdentifier']))
		{
			foreach ($this->fields as $field)
			{
				$field->onFlowUpload();
			}
			die(0);
		}
	}
	
	/**
	 * Cleanup upload temp stuff.
	 * Calling this when you display a form is enough.
	 */
	public function onCleanup()
	{
		foreach ($this->fields as $field)
		{
			$field instanceof GWF_FormsField;
			$field->onCleanup();
		}
	}
	
	public function addField(GWF_FormsField $field)
	{
		$this->fields[$field->getName()] = $field;
		$field->setForm($this);
	}
	
	public function getData()
	{
		$data = array();
		foreach ($this->fields as $field)
		{
			$field instanceof GWF_FormsField;
			if ($value = $field->getFormValue())
			{
				$data[$field->getName()] = $value;
			}
		}
		return $data;
	}
	
	public function validate()
	{
		$errors = array();
		foreach ($this->fields as $field)
		{
			if ($error = $this->validateField($field))
			{
				$field->setError($error);
				$errors[] = $error;
			}
		}
		
		return empty($errors) ? false : GWF_HTML::error($this->getTitle(), $errors);
	}
	public function validateField(GWF_FormsField $field)
	{
		if ($validator = $field->getValidator())
		{
			return call_user_func($validator, $field->getName());
		}
	}
	
	public function fields()
	{
		return $this->fields;
	}
	
	/**
	 * @param string $name
	 * @return GWF_FormsField
	 */
	public function field($name)
	{
		return $this->fields[$name];
	}
	
	public function renderField($name)
	{
		return $this->fields[$name]->render();
	}
	
	public function render()
	{
		$tVars = array(
			'form' => $this,	
		);
		return GWF_Template::templateMain('forms.php', $tVars);
	}
	
// 	public static function fromGDO(GDO $gdo)
// 	{
// 		$form = new self();
// 		foreach ($gdo->getColumnDefcache() as $c => $d)
// 		{
// 			$form->addField(GWF_Forms::fieldFromGDO($gdo, $c, $d));
// 		}
// 		return $form;
// 	}
	
// 	public static function fieldFromGDO(GDO $gdo, $field, $define)
// 	{
// 	}
}


