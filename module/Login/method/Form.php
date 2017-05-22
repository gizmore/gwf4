<?php
/**
 * @author gizmore
 */
final class Login_Form extends GWF_Method
{
	protected $_tpl = 'login.php';
//	public function isCSRFProtected() { return false; }
	
	public function getHTAccess()
	{
		return 'RewriteRule ^login/?$ index.php?mo=Login&me=Form [QSA]'.PHP_EOL;
	}
	
	public function execute()
	{
		GWF_Website::setPageTitle($this->module->lang('pt_login'));
		$result = $this->executeMethod();
		return $result;
	}
	
	public function executeMethod()
	{
		if (false !== GWF_Session::getUser())
		{
			return $this->module->error('err_already_logged_in');
		}
		if (false !== Common::getPost('login'))
		{
			return $this->onLogin();
		}
		return $this->form();
	}
	
	public function form()
	{
		$form = $this->getForm();
		$tVars = array(
			'form' => $form->templateY($this->module->lang('title_login'), $this->getAction()),
			'have_cookies' => GWF_Session::haveCookies(),
// 			'token' => $form->getFormCSRFToken(),
			'facebookUrl' => $this->getFacebookURL($this->module, Common::getAbsoluteURL('index.php?mo=Login&me=Facebook')),
            'googleUrl' => $this->getGoogleURL($this->module, Common::getAbsoluteURL('index.php?mo=Login&me=Google')),
			'tooltip' => $form->getTooltipText('bind_ip'),
			'register' => GWF_Module::loadModuleDB('Register', false, false, true) !== false,
			'recovery' => GWF_Module::loadModuleDB('PasswordForgot', false, false, true) !== false,
		);
		return $this->module->template($this->_tpl, $tVars);
	}
	
	public function getFacebookURL(Module_Login $module, $redirectURL)
	{
		if ($module->cfgFBLogin())
		{
			$permissions = ['email']; // Optional permissions
			$fb = $module->getFacebook();
			$helper = $fb->getRedirectLoginHelper();
			return $helper->getLoginUrl($redirectURL, $permissions);
		}
	}

    public function getGoogleURL(Module_Login $module, $redirectURL)
    {
        if ($module->cfgGPLogin())
        {
            $client = $module->getGoogleClient();
            $client->setRedirectUri($redirectURL);
            return $client->createAuthUrl();
        }
    }


	private $form;
	/**
	 * @return GWF_Form
	 */
	public function getForm()
	{
		if (!$this->form)
		{
			$data = array(
				'username' => array(GWF_Form::STRING, '', $this->module->lang('th_username')),
				'password' => array(GWF_Form::PASSWORD, '', $this->module->lang('th_password')),
				'bind_ip' => array(GWF_Form::CHECKBOX, true, $this->module->lang('th_bind_ip'), $this->module->lang('tt_bind_ip')),
			);
			if ($this->module->cfgCaptcha()) {
				$data['captcha'] = array(GWF_Form::CAPTCHA);
			}
			$data['login'] = array(GWF_Form::SUBMIT, $this->module->lang('btn_login'));
			$this->form = new GWF_Form($this, $data, GWF_Form::METHOD_POST, GWF_Form::CSRF_OFF);
		}
		return $this->form;
	}
	
	public function onLogin($doValidate=true)
	{
		require_once GWF_CORE_PATH.'module/Login/GWF_LoginFailure.php';
		if ($doValidate)
		{
			$form = $this->getForm();
			if (false !== ($errors = $form->validate($this->module, false))) {
				return $errors.$this->form();
			}
		}
		
		$ajax = isset($_REQUEST['ajax']);
		$username = Common::getPostString('username');
		$password = Common::getPostString('password');
		$users = GDO::table('GWF_User');
		
		
		if (false === ($user = $users->selectFirstObject('*', sprintf('user_name=\'%s\' AND user_options&%d=0', $users->escape($username), GWF_User::DELETED))))
		{
			if ($ajax) {
				return $this->module->error('err_login');
			} else {
				return $this->module->error('err_login').$this->form();
			}
		}
		elseif (true !== ($error = $this->checkBruteforce($user, false))) {
			return $ajax ? $error : $error.$this->form();
		}
		elseif (false === GWF_Hook::call(GWF_Hook::LOGIN_PRE, $user, array($password, ''))) {
			return ''; #GWF_HTML::err('ERR_GENERAL', array( __FILE__, __LINE__));
		}
		elseif (false === (GWF_Password::checkPasswordS($password, $user->getVar('user_password')))) {
			return $ajax ? $this->onLoginFailed($user, false) : $this->onLoginFailed($user, false).$this->form();
		}
		
		GWF_Password::clearMemory('password');
		
		return $this->onLoggedIn($user, false);
	}
	
	private function onLoginFailed(GWF_User $user, $isAjax)
	{
		GWF_LoginFailure::loginFailed($user);
		$time = $this->module->cfgTryExceed();
		$maxtries = $this->module->cfgMaxTries();
		list($tries, $mintime) = GWF_LoginFailure::getFailedData($user, $time);
		
		// Send alert mail?
		if ( ($tries === 1) && ($this->module->cfgAlerts()) )
		{
			$this->onSendAlertMail($user);
		}
		
		return $this->module->error('err_login2', array($maxtries-$tries, GWF_Time::humanDuration($time)));
	}
	
	private function checkBruteforce(GWF_User $user, $isAjax)
	{
		$time = $this->module->cfgTryExceed();
		$maxtries = $this->module->cfgMaxTries();
		$data = GWF_LoginFailure::getFailedData($user, $time);
		
		$tries = $data[0];
		$mintime = $data[1];
		
		if ($tries >= $maxtries) {
			return $this->module->error('err_blocked', array(GWF_Time::humanDuration($mintime - time() + $time)));
		}
		return true;
	}
	
	public function onLoggedIn(GWF_User $user, $isAjax=false)
	{
		$last_url = GWF_Session::getLastURL();
		
		if (false === GWF_Session::onLogin($user, isset($_POST['bind_ip']))) {
			return GWF_HTML::err('ERR_GENERAL', array(__FILE__, __LINE__));
		}
		
		require_once GWF_CORE_PATH.'module/Login/GWF_LoginHistory.php';
		GWF_LoginHistory::insertEvent($user->getID());
		
		# save last login time
		$user->saveVar('user_lastlogin', time());
		
		if ($this->module->cfgCleanupAlways()) {
			GWF_LoginFailure::cleanupUser($user->getID());
		}
		
// 		if (GWF_Website::isAjax())
// 		{
// 			return sprintf('1:%s', GWF_Session::getSessID());
// 		}
// 		else
		{
			GWF_Session::set('GWF_LOGIN_BACK', $last_url);
			
			if (false !== ($lang = $user->getLanguage())) {
				GWF_Language::setCurrentLanguage($lang);
			}
			
			if (0 < ($fails = GWF_LoginFailure::getFailCount($user, $this->module->cfgTryExceed()))) {
				GWF_Session::set('GWF_LOGIN_FAILS', $fails);
			}
			
			if (!GWF_Website::isAjax()) {
				GWF_Website::redirect(GWF_WEB_ROOT.'welcome');
			}
			
		}
	}
	
	private function onSendAlertMail(GWF_User $user)
	{
		if ('' === ($to = $user->getValidMail()))
		{
			return;
		}
		
		$mail = new GWF_Mail();
		$mail->setSender(GWF_BOT_EMAIL);
		$mail->setReceiver($to);
		$mail->setSubject($this->module->langUser($user, 'alert_subj'));
		$mail->setBody($this->module->langUser($user, 'alert_body', array($user->displayUsername(), $_SERVER['REMOTE_ADDR'])));
		
		return $mail->sendToUser($user);
	}
	
	#################
	### Validator ###
	#################
	public function validate_username(Module_Login $module, $arg) { return false; }
	public function validate_password(Module_Login $module, $arg) { return false; } 
}

