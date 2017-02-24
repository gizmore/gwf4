<?php
/**
 * Fake guest user.
 * @author gizmore
 * @version 4.01
 * @license MIT
 */
final class GWF_Guest extends GWF_User
{
	public static function blankUser(array $gdoData)
	{
		return new GWF_User(array_merge(array(
				'user_id' => '0',
				'user_options' => 0,
				'user_name' => null,
				'user_guest_id' => null,
				'user_guest_name' => null,
				'user_password' => null,
				'user_regdate' => null,
				'user_regip' => null,
				'user_email' => null,
				'user_gender' => GWF_User::NO_GENDER,
				'user_lastlogin' => '0',
				'user_lastactivity' => '0',
				'user_birthdate' => null,
				'user_countryid' => '0',
				'user_langid' => '0',
				'user_langid2' => '0',
				'user_level' => '0',
				'user_title' => null,
				'user_credits' => '0',
				'user_saved_at' => null,
		), $gdoData));
	}
	
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
		return self::blankUser(array(
			'user_name' => (string)$sessid,
			'user_guest_id' => (string)$sessid,
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
