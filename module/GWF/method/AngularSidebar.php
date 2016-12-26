<?php
final class GWF_AngularSidebar extends GWF_Method
{
	public function execute()
	{
		$modules = GWF_ModuleLoader::loadModulesFS();
		$modules = GWF_ModuleLoader::sortModules($modules, 'module_priority', 'DESC');
		$bars = Common::getGetString('bar', Common::getPostString('bar'));
		$result = array();
		foreach (explode(',', $bars) as $bar)
		{
			$content = '';
			foreach ($modules as $moduleName => $module)
			{
				$module instanceof GWF_Module;
				if ($module->isEnabled())
				{
					$content .= $module->sidebarContent($bar);
				}
			}
			$result[$bar] = $content;
		}
		return json_encode($result);
	}
}
