<?php
/**
 * This class is responsible to load modules from disk and install them.
 * @author gizmore
 */
final class GWF_ModuleLoader
{
	############
	### Sort ###
	############
	/**
	 * Sort an array of modules. The module array is a reference. Additionally Returns the same sorted array.
	 * @param array $modules
	 * @param string $by
	 * @param string $dir
	 */
	public static function sortModules(array &$modules, $by, $dir)
	{
		uasort($modules, array(__CLASS__, 'sort_'.$by.'_'.$dir));
		return $modules;
	}
	public static function sort_module_priority_ASC($a, $b) { return $a->getPriority() - $b->getPriority(); }
	public static function sort_module_priority_DESC($a, $b) { return $b->getPriority() - $a->getPriority(); }
	public static function sort_module_name_ASC($a, $b) { return strcasecmp($a->getName(), $b->getName()); }
	public static function sort_module_name_DESC($a, $b) { return strcasecmp($b->getName(), $a->getName()); }

	############
	### Vars ###
	############
	public static function getModuleVars($module_id)
	{
		return GDO::table('GWF_ModuleVar')->selectAll('mv_key, mv_val, mv_value, mv_type, mv_min, mv_max', 'mv_mid='.intval($module_id));
	}

	public static function saveModuleVar(GWF_Module $module, $key, $value)
	{
		if (false === ($mv = GDO::table('GWF_ModuleVar')->getRow($module->getID(), $key))) {
			return false;
		}
		if (false === ($val = self::getVarValueMV($value, $mv))) {
			return false;
		}
		return $mv->saveVars(array(
			'mv_val' => $val,
			'mv_value' => $value,
		));
	}

	public static function getVarValueMV($value, GWF_ModuleVar $mv)
	{
		return self::getVarValue($value, $mv->getVar('mv_type'), $mv->getVar('mv_min'), $mv->getVar('mv_max'), $exceed);
	}

	###################
	### Include All ###
	###################
	public static function includeAll(GWF_Module $module)
	{
		foreach ($module->getClasses() as $classname)
		{
			require_once $module->getModuleFilePath($classname.'.php');
		}
	}
	
	###############
	### Load FS ###
	###############
	public static function loadModulesFS()
	{
		if (false == ($files = @scandir(GWF_CORE_PATH.'module')))
		{
			echo GWF_HTML::err('ERR_FILE_NOT_FOUND', array('core/module'));
			return false;
		}
		$modules = array();
		foreach ($files as $name)
		{
			if ($name[0] === '.')
			{
				continue;
			}

			if (false !== ($module = GWF_Module::getModule($name)))
			{
				continue;
			}

			if (false === ($module = self::loadModuleFS($name)))
			{
				continue;
			}

			GWF_Module::$MODULES[$name] = $module;
		}
		return GWF_Module::$MODULES;
	}

	public static function loadModuleFS($name)
	{
		if (isset(GWF_Module::$MODULES[$name]))
		{
			return GWF_Module::$MODULES[$name];
		}

		$modulename = "Module_$name";
		$filename = GWF_CORE_PATH."module/$name/$modulename.php";
		if (!Common::isFile($filename))
		{
			return false;
		}
		require_once $filename;

		if (!class_exists($modulename))
		{
			return false;
		}
		$module = new $modulename();
		$module instanceof GWF_Module;
		
		$options = 0;
		$options |= $module->getDefaultAutoLoad() ? GWF_Module::AUTOLOAD : 0;
		$options |= $module->getDefaultEnabled() ? GWF_Module::ENABLED : 0;
			
		if (!($module_db = GWF_Module::loadModuleDB($name)))
		{
			$data = array(
				'module_id' => 0,
				'module_name' => $name,
				'module_priority' => $module->getDefaultPriority(),
				'module_version' => 0.0,
				'module_options' => $options,
			);
		}
		else
		{
			$data = $module_db->getGDOData();
		}

		GWF_Module::$MODULES[$name] = $module;
		$module->setGDOData($data);
		$module->loadVars();
		if ($module->isEnabled())
		{
			$module->onStartup();
		}
		return $module;
	}

	###############
	### Install ###
	###############
	public static function installModule(GWF_Module $module, $dropTables=false)
	{
//		$module->onStartup();
		$module->onLoadLanguage();
		return
			self::installModuleClasses($module, $dropTables).
			self::installModuleB($module, $dropTables).
			$module->onInstall($dropTables);
	}

	private static function installModuleClasses(GWF_Module $module, $dropTables=false)
	{
		return self::installModuleClassesB($module, $module->getClasses(), $dropTables);
	}

	public static function installModuleClassesB(GWF_Module $module, array $classnames, $dropTables=false)
	{
		$name = $module->getName();
		$back = '';
		foreach ($classnames as $classname)
		{
			require_once GWF_CORE_PATH."module/{$name}/{$classname}.php";
			$table = GDO::table($classname);
			if ($table instanceof GDO)
			{
				if (false === GDO::table($classname)->createTable($dropTables)) {
					$back .= GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
				}
			}
		}
		return $back;
	}
	
	private static function installModuleB(GWF_Module $module, $dropTables=false)
	{
		$vdb = $module->getVersionDB();
		if ($vdb == 0)
		{
			return self::installModuleC($module, $dropTables);
		}
		else
		{
			return self::upgradeModule($module, $dropTables);
		}
	}

	private static function installModuleC(GWF_Module $module, $dropTables=false)
	{
		$module->setVar('module_version', $module->getVersionFS());
		if (false === $module->replace())
		{
			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
		}
		return '';
	}

	private static function upgradeModule(GWF_Module $module, $dropTables=false)
	{
		$back = '';
		$name = $module->getName();
		$vdb = round($module->getVersionDB(), 2);
		$vfs = round($module->getVersionFS(), 2);

		if ($vdb != $vfs)
		{
			GWF_Log::logInstall(sprintf('Upgrading module %s from v%s to v%s.', $module->getName(), $vdb, $vfs));
			while ($vdb < $vfs)
			{
				$vdb = round($vdb+0.01, 2);
				$back .= self::upgradeModuleStep($module, $vdb);
			}
		}

		return $back;
	}

	private static function upgradeModuleStep(GWF_Module $module, $version)
	{
		GWF_Log::logInstall(sprintf('Upgrading module %s to v%.02f.', $module->getName(), $version));

		$name = $module->getName();
		$vstr = str_replace('.', '_', sprintf('%.02f', $version));
		$path = sprintf('%smodule/%s/Upgrade_%s_%s.php', GWF_CORE_PATH, $name, $name, $vstr);

		if (Common::isFile($path))
		{
			require_once $path;
			$func = sprintf('Upgrade_%s_%s', $name, $vstr);
			if (!function_exists($func))
			{
				return GWF_HTML::err('ERR_METHOD_MISSING', array($func, $module->display('module_name')));
			}

			$result = call_user_func($func, $module);

			if ( ($result === true) || ($result === '') || ($result === NULL))
			{
			}
			else
			{
				return $result;
			}
		}

		if (false === $module->saveVar('module_version', $version))
		{
			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
		}

		$msg = sprintf('Upgraded module %s to version %.02f.', $module->getName(), $version);
		GWF_Log::logInstall($msg);
		echo GWF_HTML::message('GWF', $msg);

		return '';
	}

	/**
	 * Install modulevars for a module. $vars is an array of $key => array($default_value, $type, $min, $max).
	 * @param GWF_Module $module
	 * @param array $vars
	 * return error message or ''
	 */
	public static function installVars(GWF_Module $module, array $vars)
	{
		$old_vars = $module->getModuleVars();

		$id = $module->getID();
		$var_t = GDO::table('GWF_ModuleVar');

		# TODO: SAFE CLEANUP
//		if (false === $var_t->deleteWhere("mv_mid=$id")) {
//			return GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
//		}

		$back = '';

		foreach ($vars as $key => $d)
		{
			$value = isset($old_vars[$key]) ? $old_vars[$key] : $d[0];
			$type = $d[1];
			$min = isset($d[2]) ? $d[2] : NULL;
			$max = isset($d[3]) ? $d[3] : NULL;

			if (false === ($val = self::getVarValue($value, $type, $min, $max))) {
				$back .= GWF_HTML::err('ERR_PARAMETER', array(__FILE__, __LINE__, '$key='.$key.', $value='.htmlspecialchars($value).', $type='.$type));
				continue;
			}

			if (false === $var_t->insertAssoc(array(
				'mv_mid' => $id,
				'mv_key' => $key,
				'mv_val' => $val,
				'mv_value' => $value,
				'mv_type' => $type,
				'mv_min' => $min,
				'mv_max' => $max,

			), true)) {
				$back .= GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
				continue;
			}
		}
		return $back;
	}

	public static function removeModuleVar(GWF_Module $module, $key)
	{
		$mid = $module->getID();
		$ekey = GDO::escape($key);
		$var_t = GDO::table('GWF_ModuleVar');
		return $var_t->deleteWhere("mv_mid={$mid} AND mv_key='{$ekey}'");
	}

	public static function getVarValue($value, $type, $min, $max, &$exceed=0)
	{
		switch ($type)
		{
			case 'time':
				$value = ''.GWF_TimeConvert::humanToSeconds($value);
				# Fallthrough

			case 'int':
				if (false === is_numeric($value)) { return false; }
				if ( ($min !== NULL) && ($value < $min) ) { $exceed = 1; return false; }
				if ( ($max !== NULL) && ($value > $max) ) { $exceed = 1; return false; }
				return (string)intval($value);
	
			case 'float':
				if (!is_numeric($value)) { return false; }
				if ( ($min !== NULL) && ($value < $min) ) { $exceed = 1; return false; }
				if ( ($max !== NULL) && ($value > $max) ) { $exceed = 1; return false; }
				return (string)floatval($value);
	
			case 'text':
				if ( ($min !== NULL) && (strlen($value) < $min) ) { $exceed = 1; return false; }
				if ( ($max !== NULL) && (strlen($value) > $max) ) { $exceed = 1; return false; }
				return $value;
	
			case 'bool':
				return self::getBoolValue($value);
	
			case 'script':
				return $value;
	
			default:
				return false;
		}
	}

	private static function getBoolValue($value)
	{
		return $value ? '1' : '0';
	}

	#############################
	### Write HT Config files ###
	#############################
	public static function installHTAccess(array $modules)
	{
		if (false === self::installHTHooks($modules)) {
			return false;
		}
		if (false === self::installHTAccess2($modules)) {
			return false;
		}
		if (false === GWF_HTAccess::createWellKnownFolder()) {
			return false;
		}
		return true;
	}

	public static function reinstallHTAccess()
	{
		return self::installHTAccess(self::loadModulesFS());
	}

	public static function installHTHooks(array $modules)
	{
		foreach ($modules as $module)
		{
			$module instanceof GWF_Module;
			if ($module->isEnabled())
			{
				$module->onAddHooks();
			}
		}
		return GWF_Hook::writeHooks();
	}

	public static function installHTAccess2(array $modules)
	{
		$hta = GWF_HTAccess::getHTAccess();
		foreach ($modules as $module)
		{
			$module instanceof GWF_Module;

			if (false === $module->isEnabled())
			{
				continue;
			}

			$hta .= '# '.$module->getName().PHP_EOL;
			$methods = self::getAllMethods($module);
			foreach ($methods as $method)
			{
				$hta .= $method->getHTAccess();
			}
			$hta .= PHP_EOL;
		}
		$hta .= GWF_HTAccess::getPostHTAccess();
		$hta = self::wrappedHTAccess($hta);
		return file_put_contents(GWF_WWW_PATH.'.htaccess', $hta);
	}

	private static function wrappedHTAccess($hta)
    {
        $prepend = self::prependHTAccess();
        $append = self::appendHTAccess();
        return $prepend . $hta . $append;
    }

    private static function prependHTAccess()
    {
        $path = GWF_PATH . '.htaccess_prepend';
        return GWF_File::isFile($path) ? file_get_contents($path) : '';
    }

    private static function appendHTAccess()
    {
        $path = GWF_PATH . '.htaccess_append';
        return GWF_File::isFile($path) ? file_get_contents($path) : '';
    }

    public static function getAllMethods(GWF_Module $module)
	{
		$back = array();
		$name = $module->getName();
		$path = GWF_CORE_PATH."module/{$name}/method";

		if (!Common::isDir($path))
		{
			return array();
		}

		if (false === ($dir = scandir($path)))
		{
			GWF4::logDie('Cannot access '.$path.' in '.__METHOD__.' line '.__LINE__);
		}

		foreach ($dir as $file)
		{
			# starts with .
			if ($file[0] === '.' || false === Common::endsWith($file, '.php'))
			{
				continue;
			}

			$path2 = $path.'/'.$file;
			if (Common::isFile($path2))
			{
				if (false === ($method = $module->getMethod(substr($file, 0, -4))))
				{
					GWF4::logDie('NO METHOD for '.$file);
				}
				$back[] = $method;
			}
		}
		return $back;
	}

	### 

	public static function sortVarsByType(array &$vars)
	{
		uasort($vars, array(__CLASS__, 'sort_vars_type'));
		return $vars;
	}

	public static function sort_vars_type($a, $b)
	{
		if (0 !== ($back = strcmp($a['mv_type'], $b['mv_type']))) {
			return $back;
		}
		return strcmp($a['mv_key'], $b['mv_key']);
	}

	###

	public static function checkModuleDependencies(GWF_Module $module)
	{
		return false;
		return $error;
	}

	###

	/**
	 * Run the cronjob for all modules.
	 * Stuff for the cron-logfile goes to stdout.
	 * Errors are redirected to stderr.
	 */
	public static function cronjobs()
	{
		GWF_Cronjob::notice('==============================');
		GWF_Cronjob::notice('=== Starting GWFv3 cronjob ===');
		GWF_Cronjob::notice('==============================');
		GWF_Log::logCron('');

		# Core jobs
		self::cronjobsCore();

		# Modules
		foreach (self::loadModulesFS() as $module)
		{
			$module instanceof GWF_Module;
			if ($module->isEnabled())
			{
				$module->onInclude();
				$module->onLoadLanguage();
				$module->onCronjob();
			}
		}

		GWF_Cronjob::notice('==============================');
		GWF_Cronjob::notice('=== Finished GWFv3 cronjob ===');
		GWF_Cronjob::notice('==============================');
	}

	private static function cronjobsCore()
	{
		self::cronjobsSession();
	}

	private static function cronjobsSession()
	{
		GWF_Cronjob::start('Session');
		$table = GDO::table('GWF_Session');
		$cut = time() - GWF_SESS_LIFETIME;
		if (false === $table->deleteWhere("sess_time<{$cut}"))
		{
			echo GWF_HTML::err('ERR_DATABASE', array(__FILE__, __LINE__));
		}
		elseif (0 < ($affected = $table->affectedRows()))
		{
			GWF_Cronjob::notice(sprintf('Deleted %s sesssions.', $affected));
		}
		GWF_Cronjob::end('Session');
	}

	public static function addColumn(GDO $gdo, $columnname)
	{
		$defs = $gdo->getColumnDefcache();
		$define = $defs[$columnname];
		return gdo_db()->createColumn($gdo->getTableName(), $columnname, $define);
	}

	public static function renameColumn(GDO $gdo, $old_columnname, $new_columnname)
	{
		return self::changeColumn($gdo, $old_columnname, $new_columnname);
	}

	public static function changeColumn(GDO $gdo, $old_columnname, $new_columnname)
	{
		$defs = $gdo->getColumnDefcache();
		$define = $defs[$new_columnname];
		return gdo_db()->changeColumn($gdo->getTableName(), $old_columnname, $new_columnname, $define);
	}
}
