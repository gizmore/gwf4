<?php
/**
 * Module overview.
 * @author gizmore
 * @since 1.0
 */
final class Admin_Modules extends GWF_Method
{
	public function getUserGroups() { return GWF_Group::ADMIN; }
	
	public function getHTAccess()
	{
		return
			sprintf('RewriteRule ^%s/?$ index.php?mo=Admin&me=Modules&by=module_name&dir=ASC [QSA]'.PHP_EOL, Module_Admin::ADMIN_URL_NAME).
			sprintf('RewriteRule ^%s/modules/by/([a-z_]+)/(DESC|ASC)/?$ index.php?mo=Admin&me=Modules&by=$1&dir=$2 [QSA]'.PHP_EOL, Module_Admin::ADMIN_URL_NAME);
	}
	
	public function execute()
	{
		return $this->module->templateNav().$this->templateModules();
	}
	
	private function templateModules()
	{
		$gdo = GDO::table('GWF_Module');
		$by = $gdo->getWhitelistedBy(Common::getGetString('by'), 'module_name');
		$dir = GDO::getWhitelistedDirS(Common::getGetString('dir', 'ASC'));
		
		$modules = $this->module->getAllModules($by, $dir);
		
		$tVars = array(
			'by' => 'module_name', 'dir' => 'ASC',
			'sortUrl' => Module_Admin::getSortURL('%BY%', '%DIR%'),
			'modules' => $modules,
			'install_all' => $this->installAllInfoLink($modules),
		);
		return $this->module->template('modules.php', $tVars);
	}

	/**
	 * A small info box with quick update link.
	 * @param array $modules
	 * @return string|string
	 * @deprecated
	 */
	private function installAllInfoLink(array $modules)
	{
		foreach ($modules as $name => $d)
		{
			if (($d['enabled']) &&  ($d['vdb'] < $d['vfs']))
			{
				return $this->module->lang('install_info', array(Module_Admin::getInstallAllURL()));
			}
		}
		return '';
	}
}
