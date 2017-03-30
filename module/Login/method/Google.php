<?php
/**
 * Class Login_Google
 * Callback for google authentication
 * @since 4.02
 */
final class Login_Google extends GWF_Method
{
    public function execute()
    {
        if (false !== GWF_Session::getUser())
		{
			return $this->module->error('err_already_logged_in');
		}

		GWF_User::getStaticOrGuest()->persistentGuest();

		if (isset($_REQUEST['error']))
        {
            return $this->module->error('err_gp_error', array(htmlspecialchars($_REQUEST['error'])));
        }

        if (!$this->module->cfgGPLogin())
        {
            return $this->module->error('err_auth_provider_disabled', array('Google+'));
        }

        return $this->onGoogleLogin($this->module, $_REQUEST['code']);
    }

    public function onGoogleLogin(Module_Login $module, $code)
    {
        $client = $module->getGoogleClient();
        $service = new Google_Service_Oauth2($client);
        echo 0;
        try {
            $client->authenticate($code);

            $token = $client->getAccessToken();
            $user = $service->userinfo->get();
            $fbVars = array(
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            );
            if ($row = GWF_FacebookToken::refresh($token, $fbVars, 'GP'))
            {
                return $module->getMethod('Form')->onLoggedIn($row->getUser());
            }
            else
            {
                return $module->error('err_client_auth');
            }
        }
        catch (Exception $exception) {
            return $this->module->error('err_gp_error', array(htmlspecialchars($exception->getMessage())));
        }
    }

}
