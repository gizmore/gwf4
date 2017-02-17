<?php
/**
 * Facebook login callback.
 * @see http://stackoverflow.com/a/34361172
 * @see GWF_FacebookToken
 * @author gizmore
 */
final class Login_Facebook extends GWF_Method
{
	public function execute()
	{
		if (false !== GWF_Session::getUser())
		{
			return $this->module->error('err_already_logged_in');
		}
		if ($this->module->cfgFBLogin())
		{
			try {
				return $this->onFacebookLogin($this->module);
			}
			catch (Exception $e) {
				return $this->module->error('err_fb_login', array(htmlspecialchars($e->getMessage())));
			}
		}
		return '';
	}

	public function onFacebookLogin(Module_Login $module)
	{
		$fb = $module->getFacebook();
		$helper = $fb->getRedirectLoginHelper();
		$accessToken = $helper->getAccessToken();
		if ($accessToken)
		{
			$response = $fb->get('/me?fields=id,name,email', $accessToken);
			if ($token = GWF_FacebookToken::refresh($accessToken->getValue(), $response->getGraphUser()->asArray()))
			{
				return $module->getMethod('Form')->onLoggedIn($token->getUser());
			}
		}
	}

}
