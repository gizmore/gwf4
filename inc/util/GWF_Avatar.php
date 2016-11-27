<?php
/**
 * Save and load images in dbimg/avatars.
 * Now supports guest avatars via session ID.
 * @version 4.0
 * @since 4.06
 * @author gizmore
 */
final class GWF_Avatar extends GDO
{
	private static $LANGFILE;
	public static function loadLanguage($path) { self::$LANGFILE = new GWF_LangTrans($path); }
	
	###########
	### GDO ###
	###########
	const NONE = 'none';
	const DEFAULT = 'default';
	const CUSTOM = 'custom';

	public function getTableName() { return GWF_TABLE_PREFIX.'avatar'; }
	public function getClassName() { return __CLASS__; }
	public function getColumnDefines()
	{
		return array(
			'avatar_id' => array(GDO::AUTO_INCREMENT),
			'avatar_user_id' => array(GDO::UINT|GDO::INDEX),
			'avatar_sess_id' => array(GDO::UINT|GDO::INDEX),
			'avatar_mode' => array(GDO::ENUM, GWF_Avatar::NONE, array(GWF_Avatar::NONE, GWF_Avatar::DEFAULT, GWF_Avatar::CUSTOM)),
			'avatar_file' => array(GDO::VARCHAR|GDO::ASCII|GDO::CASE_S, GDO::NULL, 128),
			
			'user' => array(GDO::JOIN, GDO::NULL, array('GWF_User', 'user_id', 'avatar_user_id')),
		);
	}
	
	###############
	### Getters ###
	###############
	public function getID() { return $this->getVar('avatar_id'); }
	public function getUserID() { return $this->getVar('avatar_user_id'); }
	public function getSessID() { return $this->getVar('avatar_sess_id'); }
	public function getMode() { return $this->getVar('avatar_mode'); }
	public function getFile() { return $this->getVar('avatar_file'); }
	public function getDir() { return $this->getUserID() == '0' ? 'guest' : 'user'; }
	
	###############
	### Display ###
	###############
	public static function userAvatar(GWF_User $user)
	{
		$href = htmlspecialchars(self::wwwPathForUser($user));
		$alt = $user->getName();
		return sprintf('<gwf-avatar><img src="%s" alt="%s" /></gwf-avatar>', $href, $alt);
	}
	
	##################
	### Validation ###
	##################
	public static function validateDefaultAvatar($arg)
	{
		if ($arg === '')
		{
			return false;
		}
		foreach (self::defaultAvatars() as $key => $data)
		{
			list($label, $wwwPath, $filePath, $fileName) = $data;
			if ($arg === $fileName)
			{
				return false; # All fine
			}
		}
		return self::$LANGFILE->lang('err_default_avatar');
	}
	
	public static function validateCustomAvatar($flowFile, $maxSize, array $formats)
	{
// 		if ($flowFile['size'] > $maxSize)
// 		{
// 			return self::$LANGFILE->lang('err_filesize', array($maxSize));
// 		}
		return false;
	}
	
	############
	### Save ###
	############
	public static function saveFlowAvatar(GWF_User $user, $flowFile, $defaultAvatar)
	{
		
	}
	
	
	##############
	### Static ###
	##############
	private static function modeForUser(GWF_User $user)
	{
		$avatar = self::avatarForUser($user);
		return $avatar->getMode();
	}
	
	private static function fileForUser(GWF_User $user)
	{
		$avatar = self::avatarForUser($user);
		return $avatar->getFile();
	}
	
	public static function avatarForUser(GWF_User $user)
	{
		if ($user->isGuest())
		{
			$where = sprintf('avatar_sess_id = %d', $user->getName()); # name is sessid
		}
		else
		{
			$where = sprintf('avatar_user_id = %s', $user->getID());
		}
		
		if (false === ($avatar = self::table(__CLASS__)->selectFirstObject('*', $where))) {
			$avatar = self::none($user);
		}
		return $avatar;
	}
	
	public static function none(GWF_User $user)
	{
		$userid = $user->isGuest() ? '0' : $user->getVar('user_id');
		$sessid = $user->isGuest() ? $user->getVar('user_name') : '0';
		return new self(array(
			'avatar_id' => '0',
			'avatar_user_id' => $userid,
			'avatar_sess_id' => $sessid,
			'avatar_mode' => GWF_Avatar::NONE,
			'avatar_file' => 'default.png',
		));
	}
	
	
	#############
	### Paths ###
	#############
	public static function filePathForUser(GWF_User $user)
	{
		$avatar = self::avatarForUser($user);
		return $avatar->filePath();
	}
	
	public static function wwwPathForUser(GWF_User $user)
	{
		$avatar = self::avatarForUser($user);
		return $avatar->wwwPath();
	}

	public static function wwwURLForUser(GWF_User $user)
	{
		$avatar = self::avatarForUser($user);
		return $avatar->wwwURL();
	}
	
	public function filePath()
	{
		$mode = $this->getMode();
		$file = $this->getFile();
		$dir = $this->getDir();
		if ($mode === GWF_Avatar::NONE)
		{
			$mode = GWF_Avatar::DEFAULT;
			$file = 'default.png';
		}
		
		if ($mode === GWF_Avatar::CUSTOM)
		{
			return sprintf('%sdbimg/avatar/%s', GWF_PATH, $dir, $file);
		}
		else
		{
			return sprintf('%sthemes/%s/img/default/default_avatars/%s', GWF_PATH, GWF_DEFAULT_DESIGN, $dir, $file);
		}
	}
	
	public function wwwPath()
	{
		$mode = $this->getMode();
		$file = $this->getFile();
		$dir = $this->getDir();
		if ($mode === GWF_Avatar::NONE)
		{
			$mode = GWF_Avatar::DEFAULT;
			$file = 'default.png';
		}
		return sprintf('%s/avatar/%s/%s/%s', GWF_WEB_ROOT, $dir, $mode, $file);
	}
	
	public function wwwURL()
	{
		return Common::getProtocol().'://'.GWF_DOMAIN.$this->wwwPath();
	}
	
	#######################
	### Default avatars ###
	#######################
	private static $DEFAULT_AVATARS = array();
	public static function defaultAvatars()
	{
		$path = sprintf('%1$sthemes/%2$s/img/%2$s/default_avatars', GWF_PATH, GWF_DEFAULT_DESIGN);
		GWF_File::filewalker($path, function($entry, $fullpath) {
			$key = Common::substrUntil($entry, '.');
			$label = self::$LANGFILE->lang($key);
			$wwwPath = sprintf('%sgavatar/default/%s', GWF_WWW_PATH, $entry);
			$filePath = $fullpath;
			$fileName = $entry;
			self::$DEFAULT_AVATARS[$key] = array($label, $wwwPath, $filePath, $fileName);
		});
		$avatars = self::$DEFAULT_AVATARS;
		self::$DEFAULT_AVATARS = array();
		return $avatars;
	}
	
}
