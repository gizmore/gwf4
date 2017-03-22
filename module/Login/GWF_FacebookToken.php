<?php
/**
 * Turn a gwf4 guest into a real user with oauth credentials.
 * @author gizmore
 */
final class GWF_FacebookToken extends GDO
{
    public static $PROVIDERS = array('FB', 'GP');

	###########
	### GDO ###
	###########
	public function getClassName() { return __CLASS__; }
	public function getTableName() { return GWF_TABLE_PREFIX.'facebook_login'; }
	public function getColumnDefines()
	{
		return array(
			'fb_id' => array(GDO::VARCHAR|GDO::CASE_S|GDO::ASCII|GDO::PRIMARY_KEY, GDO::NOT_NULL, 32),
            'fb_provider' => array(GDO::ENUM|GDO::PRIMARY_KEY, GDO::NOT_NULL, self::$PROVIDERS),
			'fb_uid' => array(GDO::UINT, GDO::NULL),
			'fb_token' => array(GDO::TEXT|GDO::UTF8|GDO::CASE_S, GDO::NOT_NULL),
		);
	}
	
	public function getID() { return $this->getVar('fb_id'); }
	public function getUserID() { return $this->getVar('fb_uid'); }
	public function getUser() { return GWF_User::getByID($this->getUserID()); }
	
	/**
	 * @param int $id
	 * @return GWF_FacebookToken
	 */
	public static function getByID($id)
	{
		return self::table(__CLASS__)->getBy('fb_id', $id);
	}
	
		/**
	 * @param int $id
	 * @return GWF_FacebookToken
	 */
	public static function getByUserID($userid)
	{
		return self::table(__CLASS__)->getBy('fb_uid', $userid);
	}
	
	/**
	 * Refresh login tokens and user association.
	 * @param string $token
	 * @param array $fbVars
	 * @return GWF_FacebookToken
	 */
	public static function refresh($token, array $fbVars, $provider='FB')
	{
		$id = $fbVars['id'];
		$name = "$provider-$id";
		$email = $fbVars['email'];
		$displayName = $fbVars['name'];

        if (!($user = GWF_User::getByName($name)))
        {
            $user = GWF_User::getStaticOrGuest();
            $user->persistentGuest();
        }

		$row = new self(array(
            'fb_id' => $id,
            'fb_provider' => $provider,
            'fb_uid' => $user->getID(),
            'fb_token' => $token,
		));
		$row->replace();
		
		if ($user->isGuest())
		{
			$user->saveVars(array(
 				'user_name' => $name,
				'user_guest_id' => null,
				'user_guest_name' => $displayName,
				'user_email' => $email,
				'user_options' => $user->getOptions() | GWF_User::MAIL_APPROVED,
				'user_password' => $provider,
				'user_regdate' => GWF_Time::getDate(),
			));
		}
		return $row;
	}
	
}
