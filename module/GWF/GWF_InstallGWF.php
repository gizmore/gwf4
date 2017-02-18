<?php
/**
 * Install vars.
 * Toggle module javascript protection on save var.
 * @author gizmore
 */
final class GWF_InstallGWF
{
	public static function onInstall(Module_GWF $module, $dropTables)
	{
		$result = GWF_ModuleLoader::installVars($module, array(
			# Javascript
			'AngularApp' => array('1', 'bool'),
			'MaterialApp' => array('1', 'bool'),
			'BootstrapApp' => array('0', 'bool'),
			'MinifyJavascript' => array('0', 'int', '0', '2'),
			# Fancy Config
			'FancyIndex' => array('0', 'bool'),
			'NameWidth' => array('25', 'int'),
			'DescrWidth' => array('80', 'int'),
			'IconWidth' => array('16', 'int'),
			'IconHeight' => array('16', 'int'),
			'HTMLTable' => array(false, 'bool'),
			'IgnoreClient' => array(false, 'bool'),
			'FoldersFirst' => array(true, 'bool'),
			'IgnoreCase' => array(true, 'bool'),
			'SuppressHTMLPreamble' => array(true, 'bool'),
			'ScanHTMLTitles' => array(true, 'bool'),
			'SuppressDescription' => array(true, 'bool'),
			'SuppressRules' => array(true, 'bool'),
			# Error Config
			'log' => array('404,403', 'text'),
			'mail' => array('404,403', 'text'),
			# Captcha Config
			'CaptchaBGColor' => array('FFFFFF', 'text'),
			'CaptchaFont' => array(GWF_PATH.'extra/font/teen.ttf', 'text'),
			'CaptchaWidth' => array('210', 'int'),
			'CaptchaHeight' => array('42', 'int'),
			# Security
			'allow_all_requests' => array(false, 'bool'),
			'blacklist' => array('me=ShowError;favicon.ico[^$]', 'text'),
		));
		self::toggleJavascriptProtection($module->cfgMinifyLevel());
		return $result;
	}

	##################
	### Protection ###
	##################
	public static function saveModuleVar(Module_GWF $module, $key, $value)
	{
		if ($result = $module->saveModuleVar($key, $value))
		{
			if ($key === 'MinifyJavascript')
			{
				self::toggleJavascriptProtection($value);
			}
		}
		return $result;
	}
	
	private static function toggleJavascriptProtection($value)
	{
		$filename = GWF_PATH . 'module/.htaccess';
		if ($value >= 2)
		{
			$protection = '<filesmatch "\.js)$">'.PHP_EOL.GWF_HTAccess::protectRule().'</filesmatch>'.PHP_EOL;
			file_put_contents($filename, $protection);
		}
		else
		{
			@unlink($filename);
		}
	}

}
