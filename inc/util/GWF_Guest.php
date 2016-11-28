<?php
/**
 * Fake guest user.
 * @author gizmore
 * @version 4.01
 * @license MIT
 */
final class GWF_Guest extends GWF_User
{
	public function getGuestID()
	{
		return (string)(abs($this->getID()));
	}
	
	public static function getGuest($sessid=true)
	{
		return new self(array(
			'user_id' => self::getGuestUserID($sessid),
			'user_options' => 0,
			'user_name' => GWF_HTML::lang('guest'),
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
		));
	}

	private static function getGuestUserID($sessid)
	{
		if ((!$sessid) || (!GWF_Session::hasSession()))
		{
			return '0';
		}
		else if ($sessid === true)
		{
			$sessid = GWF_Session::getSessSID();
			return "-{$sessid}";
		}
		else
		{
			return (string)$sessid;
		}
	}
}

