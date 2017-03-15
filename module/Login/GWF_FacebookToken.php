<?php
/**
 * Turn a gwf4 guest into a real user with facebook credentials.
 * @author gizmore
 */
final class GWF_FacebookToken extends GDO
{
	###########
	### GDO ###
	###########
	public function getClassName() { return __CLASS__; }
	public function getTableName() { return GWF_TABLE_PREFIX.'facebook_login'; }
	public function getColumnDefines()
	{
		return array(
			'fb_id' => array(GDO::UBIGINT|GDO::PRIMARY_KEY, GDO::NOT_NULL),
			'fb_uid' => array(GDO::UINT, GDO::NULL),
			'fb_token' => array(GDO::VARCHAR|GDO::ASCII|GDO::CASE_S, GDO::NULL, 1011),
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
	public static function refresh($token, array $fbVars)
	{
		$id = $fbVars['id'];
		$name = "FB-$id";
		$email = $fbVars['email'];
		$displayName = $fbVars['name'];
		
		# Update existing row
// 		if ($row = self::getByID($id))
// 		{
// 			$row->saveVars(array(
// 				'fb_token' => $token,
// // 				'fb_uid' => GWF_User::getStaticOrGuest()->getID(),
// 			));
// 			return $row;
// 		}
		
		# New row with re-assigned user
// 		else if ($user = GWF_User::getByName($name))
// 		{
// 			$row = new self(array(
// 				'fb_id' => $id,
// 				'fb_uid' => $user->getID(),
// 				'fb_token' => $token,
// 			));
// 			$row->insert();
// 			return $row;
// 		}
		
// 		# New row with new converted guest
// 		else
			
		$user = GWF_User::getStaticOrGuest();
		$user->persistentGuest();
		
		$row = new self(array(
				'fb_id' => $id,
				'fb_uid' => $user->getID(),
				'fb_token' => $token,
		));
		$row->replace();
		
		if ($user->isGuest())
		{
			$user->saveVars(array(
// 				'user_name' => $name,
				'user_guest_id' => null,
				'user_guest_name' => $displayName,
				'user_email' => $email,
				'user_options' => $user->getOptions() | GWF_User::MAIL_APPROVED,
				'user_password' => "FB",
				'user_regdate' => GWF_Time::getDate(),
			));
		}
		return $row;
	}
	
}
