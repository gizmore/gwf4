<?php
final class Login_LinkFacebook extends GWF_Method
{
	public function getHTAccess()
	{
		return 'RewriteRule ^link_facebook/?$ index.php?mo=Login&me=LinkFacebook [QSA]'.PHP_EOL;
	}

	public function execute()
	{
		if (isset($_GET['connectFB']))
		{
			return $this->onConnectFB() . $this->templateAccount();
		}

		return $this->templateAccount();
	}

	private function templateAccount()
	{
		$user = GWF_User::getStaticOrGuest();
		$tVars = array(
			'user' => $user,
			'facebookURL' => $this->facebookURL(),
		);
		return $this->module->template('link_facebook.php', $tVars);
	}

	private function facebookURL()
	{
		$user = GWF_User::getStaticOrGuest();
		if (GWF_FacebookToken::getByUserID($user->getID()))
		{
			return null;
		}
		$methLogin = $this->module->getMethod('Form');
		return $methLogin->getFacebookURL($this->module, Common::getAbsoluteURL('index.php?mo=Login&me=LinkFacebook&connectFB=1'));
	}

	private function onConnectFB()
	{
		$fb = $this->module->getFacebook();
		$helper = $fb->getRedirectLoginHelper();
		$accessToken = $helper->getAccessToken();
		if ($accessToken)
		{
			$response = $fb->get('/me?fields=id,name,email', $accessToken);
			if ($token = GWF_FacebookToken::refresh($accessToken->getValue(), $response->getGraphUser()->asArray()))
			{
				return $this->module->message('msg_facebook_connected');
			}
		}
		return $this->module->error('err_facebook_connect');
	}
}
