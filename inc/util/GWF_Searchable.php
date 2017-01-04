<?php
/**
 * Interface to make GDO rows and tables searchable.
 * @author gizmore
 */
interface GWF_Searchable
{
	public function getSearchableActions();
	public function getSearchableFields($action);
	public function getSearchableFormData(GWF_User $user);
}
