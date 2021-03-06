<?php
/**
 * Error pages, and Fancy indexing.
 * @author spaceone
 * @version 4.1
 * @license MIT
 */
final class Module_GWF extends GWF_Module
{
	private static $INSTANCE;
    /**
     * @return Module_GWF
     */
	public static function instance() { return self::$INSTANCE; }
	
	##############
	### Module ###
	##############
	public function getVersion() { return 4.10; }
	public function getDefaultPriority() { return 1; }
	public function getDefaultAutoLoad() { return true; }
	public function onInstall($dropTable) { require_once GWF_PATH.'module/GWF/GWF_InstallGWF.php'; return GWF_InstallGWF::onInstall($this, $dropTable); }
	public function onSavedVar($key, $value) { require_once GWF_PATH.'module/GWF/GWF_InstallGWF.php'; return GWF_InstallGWF::saveModuleVar($key, $value); }
	public function onLoadLanguage() { return $this->loadLanguage('lang/gwf'); }
	public function isCoreModule() { return true; }
	
	##############
	### Config ###
	##############
	# Javascript Config
	public function cfgAngularApp() { return $this->getModuleVarBool('AngularApp', '1'); }
	public function cfgMaterialApp() { return $this->getModuleVarBool('MaterialApp', '1'); }
	public function cfgBootstrapApp() { return $this->getModuleVarBool('BootstrapApp', '0'); }
	public function cfgMinifyLevel() { return $this->getModuleVarInt('MinifyJavascript', '0'); }
	
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
		$default = GWF_PATH.'inc/fonts/teen.ttf';
		$paths = explode(',', $this->getModuleVar('CaptchaFont', $default));
		return false === $this->validate_CaptchaFont($paths) ? (array)$default : $paths;
	}
	public function cfgCaptchaWidth() { return (int)$this->getModuleVar('CaptchaWidth', '210'); }
	public function cfgCaptchaHeight() { return (int)$this->getModuleVar('CaptchaHeight', '42'); }

	# Security Config
	public function cfgAllRequests() { return $this->getModuleVarBool('allow_all_requests', '0'); }
	
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
		self::$INSTANCE = $this;
		if ( (!Common::isCLI()) && (GWF_Session::hasSession()) && (!GWF_Website::isAjax()) )
		{
			define('GWF_MINIFY_JS', $this->cfgMinifyLevel());

			$min = GWF_MINIFY_JS >= 1 ? '.min' : '';
			
			$v = $this->getVersionDB();
			$md = $this->cfgMaterialApp();
			$ng = $this->cfgAngularApp();
			$bs = $this->cfgBootstrapApp();
				
			# CSS
			$this->addCSS("gwf4.css"); # Own core is great
			$this->addCSS("gwf4-base.css");
			if ($md) GWF_Website::addBowerCSS("angular-material/angular-material$min.css?v=$v"); # Angular material
			if ($md) GWF_Website::addCSS("https://fonts.googleapis.com/icon?family=Material+Icons"); # Icons
			if ($md) $this->addCSS("gwf-material.css"); # GWF4 css patches
			if ($ng) $this->addCSS("gwf-flow.css"); # GWF4 css patches
			if ($bs) $this->addCSS("gwf4-bootstrap.css"); # GWF4 css patches
			if ($bs) GWF_Website::addBowerCSS("bootstrap/dist/css/bootstrap$min.css?v=$v");
			if ($bs) GWF_Website::addBowerCSS("bootstrap-combobox/css/bootstrap-combobox.css?v=$v");
			if ($bs) GWF_Website::addBowerCSS("bootstrap-datepicker/dist/css/bootstrap-datepicker$min.css?v=$v");
			if ($bs) GWF_Website::addBowerCSS("bootstrap-sidebar/dist/css/sidebar.css?v=$v");
				
			# GWF Util
			$this->addJavascript('bind-polyfill.js');
			$this->addJavascript('gwf-string-util.js');
			$this->addJavascript('gwf-user.js');
			
			# GWF inline JS
			GWF_Website::addJavascriptInline($this->getConfigJS());
			GWF_Website::addJavascriptInline($this->getUserJS());
				
			# Bower JS
			GWF_Website::addBowerJavascript("jquery/dist/jquery$min.js?v=$v");
			GWF_Website::addBowerJavascript("ckeditor/ckeditor.js?v=$v");
			$this->addJavascript('jq-serialize-object.js');
			if ($bs) GWF_Website::addBowerJavascript("bootstrap/dist/js/bootstrap$min.js?v=$v");
			if ($bs) GWF_Website::addBowerJavascript("bootstrap-datepicker/dist/js/bootstrap-datepicker$min.js?v=$v");
			if ($bs) GWF_Website::addBowerJavascript("bootstrap-combobox/js/bootstrap-combobox.js?v=$v");
			if ($bs) GWF_Website::addBowerJavascript("bootstrap-sidebar/dist/js/sidebar.js?v=$v");
			if ($ng) GWF_Website::addBowerJavascript("angular/angular$min.js?v=$v");
			if ($md) GWF_Website::addBowerJavascript("angular-animate/angular-animate$min.js?v=$v");
			if ($md) GWF_Website::addBowerJavascript("angular-aria/angular-aria$min.js?v=$v");
			if ($md) GWF_Website::addBowerJavascript("angular-material/angular-material$min.js?v=$v");
			if ($md) GWF_Website::addBowerJavascript("angular-messages/angular-messages$min.js?v=$v");
			if ($ng) GWF_Website::addBowerJavascript("angular-sanitize/angular-sanitize$min.js?v=$v");
			if ($ng) GWF_Website::addBowerJavascript("angular-ui-router/release/angular-ui-router$min.js?v=$v");
			if ($ng) GWF_Website::addBowerJavascript("ng-flow/dist/ng-flow-standalone$min.js?v=$v");
			if ($ng) GWF_Website::addBowerJavascript("hamsterjs/hamster.js?v=$v");
			if ($ng) GWF_Website::addBowerJavascript("angular-mousewheel/mousewheel.js?v=$v");
			$this->addJavascript('gwf-ckeditor.js');
			if ($bs) $this->addJavascript('gwf-bootstrap.js');
				
			# GWF below here
			$this->addJavascript('gwf-ajax-sync.js');
			$this->addJavascript('gwf-bb-editor.js');
			if ($md) $this->addJavascript('gwf-angular.js');
			else if ($ng) $this->addJavascript('gwf-angular-minimal.js');
			if ($ng) $this->addJavascript('ng-enter.js');
			if ($ng) $this->addJavascript('ng-crsrup.js');
			if ($ng) $this->addJavascript('ng-html.js');
			if ($ng) $this->addJavascript('gwf-select-controller.js');
			if ($ng) $this->addJavascript('gwf-upload-controller.js');
			if ($ng) $this->addJavascript('gwf-transfer-speed-filter.js');
			if ($md) $this->addJavascript('gwf-error-service.js');
			if ($md) $this->addJavascript('gwf-exception-service.js');
			if ($md) $this->addJavascript('gwf-auth-service.js');
			if ($ng) $this->addJavascript('gwf-loading-service.js');
			if ($md) $this->addJavascript('gwf-request-service.js');
			if ($md) $this->addJavascript('gwf-ping-service.js');
			if ($md) $this->addJavascript('gwf-vibrator-service.js');
			if ($md) $this->addJavascript('gwf-request-interceptor.js');
			if ($md) $this->addJavascript('gwf-sidebar-service.js');
			if ($md) $this->addJavascript('gwf-login-dialog.js');
		}
	}
	
	private function getConfigJS()
	{
		$json = json_encode(array(
			'WEB_ROOT' => GWF_WEB_ROOT,
			'SITENAME' => GWF_SITENAME,
			'DOMAIN' => Common::getHost(),
			'MO' => Common::getGetString('mo'),
			'ME' => Common::getGetString('me'),
			'DEFAULT_MO' => GWF_DEFAULT_MODULE,
			'DEFAULT_ME' => GWF_DEFAULT_METHOD,
			'HAS_COOKIES' => GWF_Session::haveCookies(),
			'HAS_SESSION' => GWF_Session::hasSession(),
		));
		return "window.GWF_CONFIG = $json;";
	}

	public function getUserJS()
	{
		$user = GWF_User::getStaticOrGuest();
		$json = $this->getUserJSON($user);
		return "window.GWF_USER = new GWF_User($json);";
	}
	
	public function getUserData(GWF_User $user)
	{
		return array(
			'user_id' => (int)$user->getVar('user_id'),
			'user_guest_id' => (int)$user->getVar('user_guest_id'),
			'user_guest_name' => $user->getVar('user_guest_name'),
			'user_options' => (int)$user->getVar('user_options'),
			'user_name' => $user->getVar('user_name'),
			// 			'user_password' => $user->getVar('user_password'),
			'user_regdate' => $user->getVar('user_regdate'),
			'user_email' => $user->getVar('user_email'),
			'user_gender' => $user->getVar('user_gender'),
			'user_birthdate' => $user->getVar('user_birthdate'),
			'user_countryid' => (int)$user->getVar('user_countryid'),
			'user_langid' => (int)$user->getVar('user_langid'),
			'user_langid2' => (int)$user->getVar('user_langid2'),
			'user_level' => $user->getVar('user_level'),
			'user_title' => $user->getVar('user_title'),
			'user_credits' => (int)$user->getVar('user_credits'),
		);
	}
	
	public function getUserJSON(GWF_User $user)
	{
		return json_encode($this->getUserData($user));
	}
	
	###############
	### Sidebar ###
	###############
	public function sidebarContents($bars='top,left,right,bottom')
	{
		return $this->getMethod('AngularSidebar')->sidebarContents($bars);
	}
	
	public function sidebarContent($bar)
	{
		if ($bar === 'left')
		{
			return $this->template('sidebar.php');
		}
	}
	
	public function timingStats()
	{
		$db = gdo_db();

		$t_total = microtime(true)-GWF_DEBUG_TIME_START;
		$t_mysql = $db->getQueryTime();
		return array(
			'modules' => GWF_Module::getModulesLoaded(),
			'queries' => $db->getQueryCount(),
			'query_w' => $db->getQueryWriteCount(),
			't_total' => $t_total,
			't_mysql' => $t_mysql,
			't_php' => $t_total - $t_mysql,
			'memory' => GWF_Upload::humanFilesize(memory_get_peak_usage(true)),
		);
	}
}
