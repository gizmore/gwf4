<?php
final class GWF_AngularSidebar extends GWF_Method
{
	public function execute()
	{
		$content = '';
		$modules = GWF_ModuleLoader::loadModulesFS();
		$modules = GWF_ModuleLoader::sortModules($modules, 'module_priority', 'ASC');
		$bar = Common::getGetString('bar', Common::getPostString('bar'));
		foreach ($modules as $moduleName => $module)
		{
			$content .= $module->sidebarContent($bar);
		}
		return $content;
	}
}
