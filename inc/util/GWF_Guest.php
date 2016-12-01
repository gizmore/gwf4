<?php
/**
 * Fake guest user.
 * @author gizmore
 * @version 4.01
 * @license MIT
 */
final class GWF_Guest extends GWF_User
{
	public static function getGuest($sessid=true)
	{
		$sessid = self::getGuestUserID($sessid);
		return $sessid <= 0 ? self::newGuest($sessid) : self::getOrCreateGuest($sessid);
	}
	
	private static function getOrCreateGuest($sessid)
	{
		if (false !== ($user = self::loadGuest($sessid)))
		{
			return $user;
		}
		else
		{
			return self::newGuest($sessid);
		}
	}
	
	private static function loadGuest($sessid)
	{
		return self::table('GWF_User')->selectFirstObject('*', "user_guest_id=".intval($sessid));
	}
	
	private static function newGuest($sessid)
	{
		return new GWF_User(array(
			'user_id' => '0',
			'user_options' => 0,
			'user_name' => (string)$sessid,
			'user_guest_id' => (string)$sessid,
			'user_guest_name' => null,
			'user_password' => '',
			'user_regdate' => '',
			'user_regip' => GWF_IP6::getIP(GWF_IP_EXACT),
			'user_email' => '',
			'user_gender' => GWF_User::NO_GENDER,
			'user_lastlogin' => '0',
			'user_lastactivity' => time(),
			'user_birthdate' => '',
			'user_countryid' => '0',
			'user_langid' => '0',
			'user_langid2' => '0',
			'user_level' => '0',
			'user_title' => '',
			'user_settings' => '',
			'user_data' => '',
			'user_credits' => '0.00',
			'user_saved_at' => null,
		));
	}

	private static function getGuestUserID($sessid)
	{
		if ($sessid === true)
		{
			return GWF_Session::hasSession() ? (string)GWF_Session::getSessSID() : '0';
		}
		else if ($sessid <= 0)
		{
			return '0';
		}
		else
		{
			return (string)$sessid;
		}
	}
}
