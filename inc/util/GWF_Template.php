<?php
require_once 'GWF_TemplateWrappers.php';

/**
 * There are two types of templates.
 * php and smarty.
 * Smarty templates are usually faster and preferred.
 * There exist wrapper objects to call gwf stuff within smarty.
 * @todo Allow to switch designs on a per user basis.
 * @author gizmore
 * @author spaceone
 * @version 3.0
 * @since 1.0
 * @see GWF_TemplateWrappers
 */
final class GWF_Template
{
	protected static $MODULE_FILE = NULL;

	public static function getDesign() { return GWF4::getDesign(); }
	private static function pathError($path) { return GWF_HTML::err('ERR_FILE_NOT_FOUND', array( htmlspecialchars(str_replace('%DESIGN%', 'default', $path)) )); }

	public static function templatePHPMain($file, $tVars=NULL) { return self::templatePHP(GWF_WWW_PATH.'themes/%DESIGN%/'.$file, $tVars); }
	public static function templateMain($file, $tVars=NULL) { return self::templatePHPMain($file, $tVars); }
	public static function templatePHPRaw($file, $tVars=NULL) { return self::templatePHP($file, $tVars); }
	
	private static function sendErrorMail($path, $msg)
	{
		return GWF_Mail::sendDebugMail(': Smarty Error: '.$path, GWF_Debug::backtrace($msg, false));
	}

	/**
	 * Get a PHP Template output
	 * @param $path path to template file
	 * @return string
	 */
	private static function templatePHP($path, $tVars=NULL, $moduleName=NULL)
	{
		if (false === ($path2 = self::getPath($path, $moduleName)))
		{
			return self::pathError($path);
		}

// 		$tLang = isset($tVars['lang']) ? $tVars['lang'] : NULL;
		
		if (is_array($tVars))
		{
			foreach ($tVars as $key => $value)
			{
				$$key = $value;
			}
		}

		$root = GWF_WEB_ROOT;

		ob_start();
		include $path2;
		$back = ob_get_contents();
		ob_end_clean();
		return $back;
	}

	public static function moduleTemplatePHP($moduleName, $file, $tVars=NULL)
	{
		self::$MODULE_FILE = $file;
		$path = GWF_WWW_PATH.'themes/%DESIGN%/module/'.$moduleName.'/'.self::$MODULE_FILE;
		return self::templatePHP($path, $tVars, $moduleName);
	}

	/**
	 * Get the Path for the GWF Design if the file exists 
	 * @param string $path templatepath
	 * @return string|false
	 */
	private static function getPath($path, $moduleName=NULL)
	{
		// Try custom theme first.
		$path1 = str_replace('%DESIGN%', self::getDesign(), $path);
		if (file_exists($path1))
		{
			return $path1;
		}
		
		// Try module file on module templates. 
		if ($moduleName)
		{
			$path1 = GWF_CORE_PATH.'module/'.$moduleName.'/tpl/'.self::$MODULE_FILE;
		}
		else // or default theme on main templates.
		{
			$path1 = str_replace('%DESIGN%', 'default', $path);
		}
		if (file_exists($path1))
		{
			return $path1;
		}
		
		return false;
	}
}

