<?php
final class GWF_AngularSidebar extends GWF_Method
{
	public function sidebarContents($bars)
	{
		$result = array();
		$modules = GWF_Module::loadModulesDB();
		$modules = GWF_ModuleLoader::sortModules($modules, 'module_priority', 'ASC');
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
		return $result;
	}
	
	public function execute()
	{
		$bars = Common::getRequestString('bar');
		return json_encode($this->sidebarContents($bars));
	}
}
