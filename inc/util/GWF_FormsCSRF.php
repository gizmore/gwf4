<?php
class GWF_FormsCSRF extends GWF_FormsField
{
	public function __construct($strong=true)
	{
		parent::__construct(GWF_CSRF::TOKEN_NAME);
	}
	
	private function generateToken()
	{
		return md5(implode(array_keys($this->getForm()->fields())));
	}
	
	public function renderInput()
	{
		$this->setValue($this->generateToken());
		return $this->hiddenInput();
	}
	
	public function validate($name)
	{
		if (!GWF_CSRF::validateToken())
		{
			return GWF_HTML::lang('err_csrf');
		}
	}
}
