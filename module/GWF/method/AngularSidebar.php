<?php
final class GWF_AngularSidebar extends GWF_Method
{
	public function execute()
	{
		die('<h1>Login</h1>');
		$modules = GWF_ModuleLoader::loadModulesFS();
		$modules = GWF_ModuleLoader::sortModules($modules, 'priority');
		foreach ($modules as $moduleName => $module)
		{
		}
	}
}
