<?php
/**
 * Login and Logout
 * @author gizmore
 */
final class Module_Login extends GWF_Module
{
	public function getVersion() { return 4.00; }
	public function onLoadLanguage() { return $this->loadLanguage('lang/login'); }
	public function getClasses() { return array('GWF_LoginCleared', 'GWF_LoginFailure', 'GWF_LoginHistory', 'GWF_FacebookToken'); }
	public function isCoreModule() { return true; }
	public function onInstall($dropTable)
	{
		return GWF_ModuleLoader::installVars($this, array(
			'captcha' => array(false, 'bool'),
			'max_tries' => array('6', 'int', '1', '100'),
			'try_exceed' => array('600', 'time', '0', 60*60*24),
			'lf_cleanup_t' => array('1 month', 'time', '0', 60*60*24*365*8),
			'lf_cleanup_i' => array(true, 'bool'),
			'send_alerts' => array(true, 'bool'),
			'fb_login' => array(false, 'bool'),
			'fb_app_id' => array('224073134729877', 'text', 0, 31),
			'fb_secret' => array('f0e9ee41ea8d2dd2f9d5491dc81783e8', 'text', 0, 63),
		));
	}
	public function cfgCaptcha() { return $this->getModuleVarBool('captcha', '1'); }
	public function cfgMaxTries() { return $this->getModuleVarInt('max_tries', 6); } 
	public function cfgTryExceed() { return $this->getModuleVar('try_exceed', 600); }
	public function cfgCleanupTime() { return $this->getModuleVar('lf_cleanup_t', 2592000); }
	public function cfgCleanupAlways() { return $this->getModuleVarBool('lf_cleanup_i', '1'); }
	public function cfgAlerts() { return $this->getModuleVarBool('send_alerts'); }
	public function cfgFBLogin() { return $this->getModuleVarBool('fb_login'); }
	public function cfgFBAppId() { return $this->getModuleVar('fb_app_id'); }
	public function cfgFBSecret() { return $this->getModuleVar('fb_secret'); }
	
	public function onCronjob() { GWF_LoginFailure::cleanupCron($this->cfgCleanupTime()); }
	
	public function getFacebook()
	{
		if (!session_id()) { session_start(); } # lib requires normal php sessions.
		require_once $this->getModuleFilePath('php-graph-sdk/src/Facebook/autoload.php');
		return new Facebook\Facebook(array('app_id' => $this->cfgFBAppId(), 'app_secret' => $this->cfgFBSecret()));
	}
	
	public function sidebarContent($bar)
	{
		if ($bar === 'left')
		{
			return $this->templateSidebar();
		}
	}
	
	public function templateSidebar()
	{
		$this->onLoadLanguage();
		$tVars = array(
			'hrefLogin' => GWF_WEB_ROOT . 'login',
			'hrefLogout' => GWF_WEB_ROOT . 'logout',
			'form' => GWF_User::isLoggedIn() ? $this->sidebarLogin() : '',
		);
		return $this->template('sidebar.php', $tVars);
	}
	
	public function sidebarLogin()
	{
		return $this->getMethod('Form')->form();
	}
}
