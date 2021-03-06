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
	const PUBLICY = 'public';
	const MEMBERS = 'members';
	const FRIENDS = 'friends';
	const PRIVATELY = 'private';
	public static $ACL_ENUM = array(self::PUBLICY, self::MEMBERS, self::FRIENDS, self::PRIVATELY);
	
	###############
	### Defines ###
	###############
	public static function gdoDefine($default=self::PRIVATELY)
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
	public static function select($key, $selected='0')
	{
		$data = array(
			'0' => GWF_HTML::lang('acl_choose'),
			self::PUBLICY => GWF_HTML::lang('acl_public'),
			self::MEMBERS => GWF_HTML::lang('acl_members'),
			self::FRIENDS => GWF_HTML::lang('acl_friends'),
			self::PRIVATELY => GWF_HTML::lang('acl_private'),
		);
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
			case self::PUBLICY: return true;
			case self::MEMBERS: return $asker->isMember();
			case self::FRIENDS: return GWF_Friendship::areFriendsByID($asker->getID(), $objectOwnerID);
			case self::PRIVATELY: return $asker->getID() == $objectOwnerID;
			default: echo GWF_HTML::err('ERR_GENERAL', array(__FILE__, __LINE__)); return false;
		}
	}
	
}
