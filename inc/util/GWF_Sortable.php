<?php
/**
 * Interface for sortable GDO classes.
 * @author gizmore
 */
interface GWF_Sortable extends GWF_Displayable
{
	public function getSortableDefaultBy(); # return string
	public function getSortableDefaultDir(); # return string
	public function getSortableFields($action); # return array(colnames)
}
