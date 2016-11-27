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
			'avatar_user_id' => array(GDO::UINT|GDO::INDEX, '0'),
			'avatar_sess_id' => array(GDO::UINT|GDO::INDEX, '0'),
			'avatar_mode' => array(GDO::ENUM, GWF_Avatar::NONE, array(GWF_Avatar::NONE, GWF_Avatar::DEFAULT, GWF_Avatar::CUSTOM)),
			'avatar_file' => array(GDO::VARCHAR|GDO::ASCII|GDO::CASE_S, GDO::NULL, 128),
			'avatar_version' => array(GDO::UINT, '0'),
			
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
	public function getVersion() { return $this->getVar('avatar_version'); }
	public function getVersionInt() { return (int) $this->getVersion(); }
	public function getDestinationID()  { return $this->isGuest() ? $this->getVar('avatar_sess_id') : $this->getVar('avatar_user_id'); }
	public function isGuest() { return $this->getVar('avatar_sess_id') !== '0'; }
	public function isCustomAvatar() { return $this->getMode() === self::CUSTOM; }
	public function isDefaultAvatar() { return $this->getMode() === self::DEFAULT; }
	public function getDefaultAvatarFilename() { return $this->isDefaultAvatar() ? $this->getFile() : ''; }
	
	###############
	### Display ###
	###############
	public static function userAvatar(GWF_User $user)
	{
		$href = htmlspecialchars(self::wwwPathForUser($user));
		$alt = $user->getName();
		return sprintf('<gwf-avatar><img src="%s" alt="%s" /></gwf-avatar>', $href, $alt);
	}
	
	public static function defaultAvatar(array $avatar)
	{
		list($label, $wwwPath, $filePath, $fileName) = $avatar;
		return sprintf('<gwf-avatar><img src="%s" alt="%s" /></gwf-avatar>', $wwwPath, $label);
	}
	
	##################
	### Validation ###
	##################
	public static function isValidDefaultAvatar($arg)
	{
		foreach (self::defaultAvatars() as $key => $data)
		{
			list($label, $wwwPath, $filePath, $fileName) = $data;
			if ($arg === $fileName)
			{
				return true;
			}
		}
		return false;
	}

	public static function validateDefaultAvatar($arg)
	{
		if ($arg === '')
		{
			return false;
		}
		if (self::isValidDefaultAvatar($arg))
		{
			return false; # All fine
		}
		return self::$LANGFILE->lang('err_default_avatar');
	}
	
	public static function validateCustomAvatar($flowFile, $maxSize, array $formats)
	{
		if (!$flowFile)
		{
			return false;
		}
		if ($flowFile['size'] > $maxSize)
		{
			return self::$LANGFILE->lang('err_filesize', array(GWF_File::humanFilesize($maxSize)));
		}
		$mime = mime_content_type(file_get_contents($flowFile['path']));
		if (!Common::startsWith($mime, 'image'))
		{
			return self::$LANGFILE->lang('err_no_image', array(implode(', ', $formats)));
		}
		$mime = strtolower(Common::substrFrom($mime, '/'));
		if (!in_array($mime, $formats, true))
		{
			return self::$LANGFILE->lang('err_no_image', array(implode(', ', $formats)));
		}
		
		return false;
	}
	
	############
	### Save ###
	############
	public static function saveFlowAvatar(GWF_User $user, $flowFile, $defaultAvatar)
	{
		if ($flowFile && $defaultAvatar)
		{
			return false;
		}
		
		$avatar = self::avatarForUser($user);
		$data = array(
			'avatar_mode' => self::NONE,
			'avatar_file' => null,
			'avatar_version' => ''.($avatar->getVersionInt() + 1), # inc version
		);
		
		if ($flowFile)
		{
			$data['avatar_mode'] = self::CUSTOM;
			$data['avatar_file'] = $flowFile['name'];
			$dir = $avatar->getDir();
			if (GWF_File::isFile($flowFile['path']))
			{
				$destinationDir = sprintf('%sdbimg/avatar/%s/%s', GWF_PATH, $dir, $avatar->getDestinationID());
				$destinationPath = $destinationDir.'/'.$flowFile['name'];
				if (!GWF_File::createDir($destinationDir))
				{
					GWF_Log::logError('Cannot create dir: '-$destinationDir);
					return false;
				}
				if (!@copy($flowFile['path'], $destinationPath))
				{
					GWF_Log::logError('Cannot copy to dest: '.$destinationPath);
					return false;
				}
			}
			else
			{
				GWF_Log::logError('Cannot read source file: '.$flowFile['path']);
				return false;
			}
		}
		
		else if ($defaultAvatar)
		{
			if (self::isValidDefaultAvatar($defaultAvatar))
			{
				$data['avatar_mode'] = self::DEFAULT;
				$data['avatar_file'] = $defaultAvatar;
			}
		}
		
		if ($avatar->getID() <= 0)
		{
			$avatar->insert();
		}
		
		return $avatar->saveVars($data);
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
			'avatar_file' => null,
			'avatar_version' => '0',
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
			$file = 'default.jpeg';
		}
		
		if ($mode === GWF_Avatar::CUSTOM)
		{
			return sprintf('%sdbimg/avatar/%s/%s', GWF_PATH, $dir, $file);
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
			$file = 'default.jpeg';
		}
		
		if ($mode === GWF_Avatar::CUSTOM)
		{
			return sprintf('%savatar/%s/%s/%s/%s?v=%s', GWF_WEB_ROOT, $mode, $dir, $this->getDestinationID(), $file, $this->getVersion());
		}
		else
		{
			return sprintf('%savatar/%s/%s', GWF_WEB_ROOT, $mode, $file);
		}
	}
	
	public function wwwURL()
	{
		return Common::getProtocol().'://'.GWF_DOMAIN.$this->wwwPath();
	}
	
	#######################
	### Default avatars ###
	#######################
	private static $DEFAULT_AVATARS = array();
	public static function defaultAvatars(GWF_User $user)
	{
		# Filewalker args
		$avatar = self::avatarForUser($user);
		$default = $avatar->getDefaultAvatarFilename();
		$args = array($user, $avatar, $default);
		# Default avatars directory
		$path = sprintf('%1$sthemes/%2$s/img/%2$s/default_avatars', GWF_PATH, GWF_DEFAULT_DESIGN);
		# Add them via filewalking
		GWF_File::filewalker($path, array(__CLASS__, 'addDefaultAvatar'), $args);
		# Return and empty static helper var
		$avatars = self::$DEFAULT_AVATARS;
		self::$DEFAULT_AVATARS = array();
		return $avatars;
	}
	
	public static function addDefaultAvatar($entry, $fullpath, $args)
	{
		list($user, $avatar, $default) = $args;
		$key = Common::substrUntil($entry, '.');
		$label = self::$LANGFILE->lang($key);
		$wwwPath = sprintf('%savatar/default/%s', GWF_WEB_ROOT, $entry);
		$filePath = $fullpath;
		$fileName = $entry;
		$selected = $default === $entry;
		self::$DEFAULT_AVATARS[$key] = array($label, $wwwPath, $filePath, $fileName, $selected);
	}
	
}
