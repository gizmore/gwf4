<?php
/**
 * Categories. Could be a built-in class.
 * @author gizmore
 */
final class Module_Category extends GWF_Module
{
	public function getVersion() { return 4.00; }
	public function getDefaultPriority() { return 30; }
	public function onLoadLanguage() { return $this->loadLanguage('lang/category'); }
	public function getClasses() { return array('GWF_Category', 'GWF_CategorySelect', 'GWF_CategoryTranslation'); }
	public function getAdminSectionURL() { return $this->getMethodURL('Admin'); }
}
