<?php
/**
 * Access Control List utility.
 * Unlike other solutions there is no extra table added except GWF_Friendship.
 * Your access is hold in you objects and you feed access and ownerID to hasPermission.
 * 
 * @author gizmore
 * @license MIT
 */
final class GWF_ACL
{
	#############
	### Const ###
	#############
	const PUBLIC = 'public';
	const MEMBERS = 'members';
	const FRIENDS = 'friends';
	const PRIVATE = 'private';
	public static $ACL_ENUM = array(self::PUBLIC, self::MEMBERS, self::FRIENDS, self::PRIVATE);
	
	###############
	### Defines ###
	###############
	public static function gdoDefine($default=self::PRIVATE)
	{
		return array(GDO::ENUM, $default, self::$ACL_ENUM);
	}
	
	public static function formDefine($key, $selected='', $tt_acl=null)
	{
		return array(GWF_Form::SELECT, self::select($key, $selected), $tt_acl ? $tt_acl : GWF_HTML::lang('tt_acl'));
	}
	
	##############
	### Select ###
	##############
	public static function select($key, $selected)
	{
		$data = array();
		$data[] = array('', GWF_HTML::lang('acl_choose'));
		$data[] = array(self::PUBLIC, GWF_HTML::lang('acl_public'));
		$data[] = array(self::MEMBERS, GWF_HTML::lang('acl_members'));
		$data[] = array(self::FRIENDS, GWF_HTML::lang('acl_friends'));
		$data[] = array(self::PRIVATE, GWF_HTML::lang('acl_private'));
		return GWF_Select::display($key, $data, $selected, '', GWF_HTML::lang('th_acl'));
	}
	
	public static function validatePost($arg)
	{
		if (!in_array($arg, self::$ACL_ENUM, true))
		{
			return GWF_HTML::lang('err_acl_value');
		}
		return false;
	}
	
	##################
	### Permission ###
	##################
	public static function hasPermission(GWF_User $asker, $objectOwnerID, $access)
	{
		switch($access)
		{
			case self::PUBLIC: return true;
			case self::MEMBERS: return $asker->isMember();
			case self::FRIENDS: return GWF_Friendship::areFriendsByID($asker->getID(), $objectOwnerID);
			case self::PRIVATE: return $asker->getID() == $objectOwnerID;
			default: echo GWF_HTML::err('ERR_GENERAL', array(__FILE__, __LINE__)); return false;
		}
	}
	
}
