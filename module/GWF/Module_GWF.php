<?php
/**
 * Error pages, and Fancy indexing.
 * @author gizmore
 * @author spaceone
 * @version 3.0
 * @since 1.0
 */
final class Module_GWF extends GWF_Module
{
	public function getVersion() { return 4.01; }
	public function getDefaultPriority() { return 1; }
	public function getDefaultAutoLoad() { return true; }
	public function onInstall($dropTable) { require_once GWF_CORE_PATH.'module/GWF/GWF_InstallGWF.php'; return GWF_InstallGWF::onInstall($this, $dropTable); }
	public function onLoadLanguage() { return $this->loadLanguage('lang/gwf'); }

	# Fancy Config
	public function cfgFancyIndex() { return $this->getModuleVarBool('FancyIndex', '0'); }
	public function cfgNameWidth() { return $this->getModuleVar('NameWidth', '25'); }
	public function cfgDescriptionWidth() { return $this->getModuleVar('DescrWidth', '80'); }
	public function cfgIconWidth() { return $this->getModuleVar('IconWidth', '16'); }
	public function cfgIconHeight() { return $this->getModuleVar('IconHeight', '16'); }
	public function cfgHTMLTable() { return $this->getModuleVarBool('HTMLTable','0'); }
	public function cfgIgnoreClient() { return $this->getModuleVarBool('IgnoreClient', '0'); }
	public function cfgFoldersFirst() { return $this->getModuleVarBool('FoldersFirst', '1'); }
	public function cfgIgnoreCase() { return $this->getModuleVarBool('IgnoreCase', '1'); }
	public function cfgSuppressHTMLPreamble() { return $this->getModuleVarBool('SuppressHTMLPreamble', '1'); }
	public function cfgScanHTMLTitles() { return $this->getModuleVarBool('ScanHTMLTitles','1'); }
	public function cfgSuppressDescription() { return $this->getModuleVarBool('SuppressDescription', '1'); }
	public function cfgSuppressRules() { return $this->getModuleVarBool('SuppressRules', '1'); }
	
	# Error Config
	public function cfgLog() { return $this->filterCodeVar('log', '403,404'); }
	public function cfgMail() { return $this->filterCodeVar('mail', '403,404'); }
	public function cfgBlacklist() { return $this->getModuleVar('blacklist', 'me=ShowError;favicon.ico[^$]'); }
	private function filterCodeVar($var, $default) { return str_replace(' ', '', $this->getModuleVar($var, $default)); }
	
	# Captcha Config
	public function cfgCaptchaBG() { $bgcolor = $this->getModuleVar('CaptchaBGColor', 'FFFFFF'); return false === $this->validate_CaptchaColor($bgcolor) ? 'FFFFFF' : $bgcolor;; }
	public function cfgCaptchaFont()
	{
		$default = GWF_PATH.'extra/font/teen.ttf';
		$paths = explode(',', $this->getModuleVar('CaptchaFont', $default));
		return false === $this->validate_CaptchaFont($paths) ? (array)$default : $paths;
	}
	public function cfgCaptchaWidth() { return (int)$this->getModuleVar('CaptchaWidth', '210'); }
	public function cfgCaptchaHeight() { return (int)$this->getModuleVar('CaptchaHeight', '42'); }

	# Security Config
	public function cfgAllRequests() { return $this->getModuleVarBool('allow_all_requests', false); }
	
	public static function validate_CaptchaColor($color) { return preg_match('/^[a-f0-9A-F]{6}$/D', $color) ? $color : false; }
	public static function validate_CaptchaFont(array $paths)
	{
		foreach($paths as $path)
			if(false === is_file( realpath($path) ))
				return false;
		return $paths;
	}

	public function onStartup()
	{
		$min = GWF_DEBUG_JS ? '' : '.min';
		$this->addJavascript('gwf-user.js');
		GWF_Website::addJavascriptInline($this->getConfigJS());
		GWF_Website::addJavascriptInline($this->getUserJS());
		GWF_Website::addBowerJavascript("jquery/dist/jquery$min.js");
		GWF_Website::addBowerJavascript("angular/angular$min.js");
		GWF_Website::addBowerJavascript("angular-animate/angular-animate$min.js");
		GWF_Website::addBowerJavascript("angular-aria/angular-aria$min.js");
		GWF_Website::addBowerJavascript("angular-material/angular-material$min.js");
		GWF_Website::addBowerJavascript("angular-messages/angular-messages$min.js");
		GWF_Website::addBowerJavascript("angular-ui-router/release/angular-ui-router$min.js");
		$this->addJavascript('gwf-angular.js');
		$this->addJavascript('RequestSrvc.js');
		$this->addJavascript('PingSrvc.js');
		$this->addJavascript('ngEnter.js');
		$this->addJavascript('RequestInterceptor.js');
		}
	
	private function getConfigJS()
	{
		$json = json_encode(array(
			'WEB_ROOT' => GWF_WEB_ROOT,
		));
		return "var GWF_CONFIG = $json;";
	}

	private function getUserJS()
	{
		$user = GWF_User::getStaticOrGuest();
		$json = json_encode(array(
				'user_id' => (int)$user->getVar('user_id'),
				'user_options' => (int)$user->getVar('user_options'),
				'user_name' => $user->getVar('user_name'),
				'user_password' => $user->getVar('user_password'),
				'user_regdate' => $user->getVar('user_regdate'),
				'user_email' => $user->getVar('user_email'),
				'user_gender' => $user->getVar('user_gender'),
				'user_birthdate' => $user->getVar('user_birthdate'),
				'user_avatar_v' => (int)$user->getVar('user_avatar_v'),
				'user_countryid' => (int)$user->getVar('user_countryid'),
				'user_langid' => (int)$user->getVar('user_langid'),
				'user_langid2' => (int)$user->getVar('user_langid2'),
				'user_level' => (int)$user->getVar('user_level'),
				'user_title' => $user->getVar('user_title'),
				'user_credits' => round($user->getVar('user_credits'), 2),
		));
		return "var GWF_USER = new GWF_User($json);";
	}
	}
