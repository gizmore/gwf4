<?php
/**
 * Interface of Displayble GDO classes.
 * @deprecated
 * @author gizmore
 */
interface GWF_Displayable
{
	public function getDisplayableFields($action);
	public function displayColumn($field);
}
