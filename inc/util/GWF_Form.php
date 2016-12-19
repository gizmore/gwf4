<?php
/**
 * Very convinient form generation. [0]=TYPE,[1]=value,[2]=title,[3]=Tooltip,[4]=LEN?,[5]=required
 * @author gizmore
 * @version 3.0
 * @since 2.0
 */
class GWF_Form
{
	# Methods
	const METHOD_GET = 'get';
	const METHOD_POST = 'post';

	# Encoding Types
	const ENC_DEFAULT   = 'application/x-www-form-urlencoded';
	const ENC_MULTIPART = 'multipart/form-data';

	# Form array offsets
	const TYPE = 0;
	const VALUE = 1;
	const TITLE = 2;
	const TOOLTIP = 3;
	const LENGTH = 4;
	const REQUIRED = 5;

	# Data Types
	const INT = 1;
	const DATE = 2;
	const FLOAT = 3;
	const STRING = 4;
	const STRING_NO_CHECK = 5;
	const SSTRING = 6;
	const MESSAGE = 7;
	const MESSAGE_NOBB = 8;
	const PASSWORD = 9;
	const CHECKBOX = 10;
	const CAPTCHA = 11;
	const SUBMIT = 12;
	const SUBMITS = 13;
	const FILE = 14;
	const FILE_OPT = 15;
	const HIDDEN = 16;
	const SELECT = 17;
	const DIVIDER = 18;
	const VALIDATOR = 19;
	const HEADLINE = 20;
	const DATE_FUTURE = 21;
	const SELECT_A = 22;
	const SUBMIT_IMG = 23;
	const SUBMIT_IMGS = 24;
	const TIME = 25;
//	const HIDDEN_NO_CHECK = 26;
	const HTML = 27;
	const ENUM = 28; # array(array(key,value),array(key,value),...)
// 	const ENUM_ASSOC = 29;
	const FILE_IMAGE = 30;
	const FILE_IMAGES = 31;
	

	# CSRF protection levels.
	const CSRF_OFF = 0;
	const CSRF_WEAK = 1;
	const CSRF_STRONG = 2;

	private $method;
	private $validator;
	private $form_data;
	private $csrf_bit;
	
	private static $SUBMITTED = false;

	/**
	 * $data is [0]=TYPE,[1]=value,[2]=title,[3]=Tooltip,[4]=LEN?,[5]=required
	 * @param callback $validator
	 * @param array $data
	 */
	public function __construct($validator, array $data, $method=self::METHOD_POST, $csrf_bit=self::CSRF_STRONG)
	{
		$this->validator = $validator;
		$this->form_data = $data;
		$this->method = $method;
		$this->csrf_bit = $csrf_bit;
	}

	public function getMethod() { return $this->method; }
	public function getCSRFLevel() { return $this->csrf_bit; }
	public function getFormData() { return $this->form_data; }
	public function getFormDataFor($key) { return $this->form_data[$key]; }
	public function getFormCSRFToken() { return $this->form_data[GWF_CSRF::TOKEN_NAME][self::VALUE]; }
	public function getTooltipText($key) { return isset($this->form_data[$key][self::TOOLTIP]) ? $this->form_data[$key][self::TOOLTIP] : ''; }

	public function getVar($key, $default=false)
	{
		if (!isset($this->form_data[$key]))
		{
			return $default;
		}
		return $this->method === self::METHOD_POST ? $this->getPostVar($key, $default) : $this->getGetVar($key, $default);
	}

	private function getPostVar($key, $default)
	{
		switch($this->form_data[$key][0])
		{
			case self::VALIDATOR:
				return false;
				
			case self::FILE: case self::FILE_OPT: case self::FILE_IMAGE:
				return $this->getFile($key, $default);
				
			case self::FILE_IMAGES:
				return $this->getFiles($key, $default);
	
			case self::DATE: case self::DATE_FUTURE:
				return $this->getDate($key, $this->form_data[$key][4], $_POST);
	
			case self::TIME:
				return sprintf('%02s%02s', Common::getPostString($key.'h', '00'), Common::getPostString($key.'i', '00'));

			case self::CHECKBOX:
				return isset($_POST[$key]);

			case self::INT:
				return Common::getPostInt($key, $default);

			case self::SELECT_A:
				return Common::getPostArray($key, $default);

			default:
				return Common::getPostString($key, $default);
		}
	}

	private function getGetVar($key, $default)
	{
		switch($this->form_data[$key][0])
		{
			case self::VALIDATOR:
				return false;
				
			case self::FILE: case self::FILE_OPT: case self::FILE_IMAGE: case self::FILE_IMAGES:
				return $default;
	
			case self::DATE: case self::DATE_FUTURE:
				return $this->getDate($key, $this->form_data[$key][4], $_GET);
	
			case self::TIME:
				return sprintf('%02s%02s', Common::getGetString($key.'h', '00'), Common::getGetString($key.'i', '00'));

			case self::CHECKBOX:
				return isset($_GET[$key]);

			case self::INT:
				return Common::getGetInt($key, $default);

			case self::SELECT_A:
				return Common::getGetArray($key, $default);

			default:
				return Common::getGetString($key, $default);
		}
	}

	private function getDate($key, $len, array $array)
	{
		$back = '';
		switch ($len)
		{
			case 14: $back = $array[$key.'s'].$back;
			case 12: $back = $array[$key.'i'].$back;
			case 10: $back = $array[$key.'h'].$back;
			case 8: $back = $array[$key.'d'].$back;
			case 6: $back = $array[$key.'m'].$back;
			case 4: $back = $array[$key.'y'].$back;
				break;
			default: die('Form Date Length is invalid for '.$key);
		}
		return $back;
	}

	public function validate($context)
	{
		self::$SUBMITTED = true;
		if (false !== ($error = GWF_FormValidator::validate($context, $this, $this->validator)))
		{
			return $error;
		}
		$this->onNewCaptcha();
		return false;
	}

	public static function validateCSRF_WeakS()
	{
		if (false === ($token = GWF_CSRF::validateToken()))
		{
			return GWF_HTML::err('ERR_CSRF');
		}
		return false;
	}

	public function templateX($title='', $action=true)
	{
		return $this->template('formX.php', $title, $action, $this->computeColspanX());
	}

	public function templateY($title='', $action=true)
	{
		return $this->template('formY.php', $title, $action, 3);
	}

	private function template($file, $title, $action=true, $colspan)
	{
		if (is_bool($action))
		{
			$action = $_SERVER['REQUEST_URI'];
		}

		$tVars = array(
			'data' => $this->getTemplateData(),
			'title' => $title,
			'action' => htmlspecialchars($action),
			'method' => $this->method,
			'enctype' => $this->getEncType(),
			'has_files' => $this->hasFiles(),
			'colspan' => $colspan,
		);

		return GWF_Template::templatePHPMain($file, $tVars);
	}

	private function computeColspanX()
	{
		return count($this->form_data) + 1;
	}
	
	public function hasFiles()
	{
		foreach ($this->form_data as $key => $data)
		{
			if ( ($data[0] === self::FILE) || ($data[0] === self::FILE_OPT) )
			{
				return true;
			}
		}
		return false;
	}

	private function getEncType()
	{
		return self::hasFiles() ? self::ENC_MULTIPART : self::ENC_DEFAULT;
	}

	private function getTemplateData()
	{
		foreach ($this->form_data as $key => $data)
		{
			# Setup input
			switch ($data[0])
			{
				case self::CAPTCHA:
					$this->form_data[$key][1] = $this->getCaptchaData();
					break;

				case self::DATE:
					$this->form_data[$key][1] = GWF_DateSelect::getDateSelects($key, $data[1], $data[4], false, false, false);
					break;
		
				case self::DATE_FUTURE:
					$this->form_data[$key][1] = GWF_DateSelect::getDateSelects($key, $data[1], $data[4], false, true, false);
					break;
		
				case self::TIME:
					$this->form_data[$key][1] = GWF_TimeSelect::select($key.'h', $key.'i', $data[1]);
					break;
				
				case self::ENUM:
					$this->form_data[$key][1] = GWF_Select::display($key, $data[4], $this->getVar($key, $data[1]));
					break;
		
				case self::SELECT:
				case self::SELECT_A:
				case self::SUBMIT:
				case self::SUBMITS:
				case self::SUBMIT_IMG:
				case self::SUBMIT_IMGS:
				case self::HEADLINE:
				case self::DIVIDER:
				case self::VALIDATOR:
					break;
		
				case self::HIDDEN:
					$this->form_data[$key][1] = htmlspecialchars($this->form_data[$key][1]);
					break;
		
				case self::CHECKBOX:
					# TODO: Optimize for size. Tricky, as $this->getVar() is not always appropiate for overwriting, because bool=isset or notset.
					$arr = $this->method === self::METHOD_GET ? $_GET : $_POST;
					if (isset($arr[$key]))
					{
						$this->form_data[$key][1] = true;
					}
					elseif (self::$SUBMITTED)
					{
						$this->form_data[$key][1] = false;
					}
					break;
		
				case self::FILE:
				case self::FILE_OPT:
				case self::FILE_IMAGE: case self::FILE_IMAGES:
				case self::HTML:
					break;
		
				default:
					if (false !== ($v = $this->getVar($key)))
					{
						$this->form_data[$key][1] = $v;
					}
		
					if (true === is_array($this->form_data[$key][1]))
					{
					# recursion needet?
					// 	function(&$a) : $a =  htmlspecialchars($a);
					//	function(&$b) : $b =  is_array($b) ? self::arrayescape($b) : htmlspecialchars($b);

					//	array_walk_recursive($this->form_data[$key][$1], array('GWF_Form', 'arrayescape'));
					//	array_map(array('GWF_Form', 'arrayescape'), $this->form_data[$key][1]);
					}
					else
					{
						$this->form_data[$key][1] = htmlspecialchars($this->form_data[$key][1]);
					}
					break;
			}

			# Setup required
//			if (!isset($data[self::REQUIRED]))
//			{
//				switch ($data[0])
//				{
//					case self::STRING:
//						$data[self::REQUIRED] = true;
//						break;
//				}
//			}
		}

		if ($this->csrf_bit > self::CSRF_OFF)
		{
			$this->form_data[GWF_CSRF::TOKEN_NAME] = array(self::HIDDEN, GWF_CSRF::generateToken($this->getCSRFToken()));
		}

		return $this->form_data;
	}

	############
	### CSRF ###
	############
	public function getCSRFToken()
	{
		$hash = '';
		foreach ($this->form_data as $k => $v)
		{
			switch ($v[0])
			{
				# skip these
//				case self::SPECIAL_OPT:
				case self::FILE_OPT:
				case self::SUBMIT:
				case self::SUBMIT_IMG:
				case self::SUBMITS:
				case self::SSTRING:
				case self::HEADLINE:
					break;
				default:
					$hash .= '_'.$k;
			}
		}

		return GWF_Password::getToken($hash);
	}

	###############
	### Captcha ###
	###############
	const SESS_NEXT_CAPTCHA = 'GWF4FNC';
	public function onNewCaptcha()
	{
		GWF_Session::remove(self::SESS_NEXT_CAPTCHA);
		GWF_Session::remove('php_captcha');
	}

	public function onSolvedCaptcha()
	{
		GWF_Session::set(self::SESS_NEXT_CAPTCHA, GWF_Session::get('php_captcha'));
	}

	private function getCaptchaData()
	{
		return GWF_Session::getOrDefault(self::SESS_NEXT_CAPTCHA, '');
	}

	##############
	### Render ###
	##############
	public static function start($action=true, $encoding=self::ENC_DEFAULT, $method='post', $usecsrf=true)
	{
		if (is_bool($action))
		{
			$action = $_SERVER['REQUEST_URI'];
		}

		if ($encoding !== self::ENC_DEFAULT && $encoding !== self::ENC_MULTIPART)
		{
			echo GWF_HTML::error('GWF_Form', 'Unknown Form Encoding 0815-F1');
			$encoding = self::ENC_DEFAULT;
		}

		return
			'<div>'.PHP_EOL.
			sprintf('<form action="%s" enctype="%s" method="%s">', htmlspecialchars($action), $encoding, htmlspecialchars($method)).PHP_EOL.
			($usecsrf ? sprintf('<div>%s</div>', GWF_CSRF::hiddenForm('')).PHP_EOL : '');
	}

	public static function end()
	{
		return 
			'</form>'.PHP_EOL.
			'</div>'.PHP_EOL;
	}

	public static function hidden($key, $value)
	{
		return sprintf('<input type="hidden" name="%s" value="%s" />', htmlspecialchars($key), htmlspecialchars($value));
	}

	public static function buttonImage($key, $src)
	{
		return sprintf('<input type="image" name="%s" src="%s" />', htmlspecialchars($key), GWF_WEB_ROOT.htmlspecialchars($src));
	}

	public static function captcha()
	{
		return
			sprintf('<img src="%sCaptcha/%s" onclick="this.src=\'%sCaptcha/?\'+(new Date()).getTime();" />'.PHP_EOL, 
			GWF_WEB_ROOT, '?v='.time(), GWF_WEB_ROOT);
	}

	public static function checkbox($name, $checked=false, $id='', $onclick='')
	{
		$name = htmlspecialchars($name);
		$checked = GWF_HTML::checked($checked);
		$id = $id === '' ? '' : sprintf('id="%s"', htmlspecialchars($id));
		$onclick = ''; # TODO: onclick
		return sprintf('<input type="checkbox" %s name="%s" %s %s />', $id, $name, $checked, $onclick);
	}

	public static function submit($name, $text='', $id='', $onclick='')
	{
		$id = $id === '' ? '' : sprintf(' id="%s"', htmlspecialchars($id));
		$name = htmlspecialchars($name);
		$text = htmlspecialchars($text);
		return sprintf('<span><input%s type="submit" name="%s" value="%s" /></span>', $id, $name, $text);
	}

	###################
	### Flow upload ###
	###################
	private function getTempDir($key='')
	{
		return GWF_PATH.'temp/flow/'.GWF_Session::getSessSID().'/'.$key;
	}
	
	private function getChunkDir($key)
	{
		$chunkFilename = preg_replace('#[\\/]#', '', $_REQUEST['flowFilename']);
		return $this->getTempDir($key).'/'.$chunkFilename;
	}
	
	private function denyFlowFile($key, $file, $reason)
	{
		return @file_put_contents($this->getChunkDir($key).'/denied', $reason);
	}
	
	private function deniedFlowFile($key, $file)
	{
		$file = $this->getChunkDir($key).'/denied';
		return GWF_File::isFile($file) ? file_get_contents($file) : false;
	}
	
	private function getFile($key, $default)
	{
		return array_unshift($this->getFiles($key, $default));
	}
	
	private function getFiles($key, $default)
	{
		$path = $this->getTempDir($key);
		if (false === ($dir = @dir($path)))
		{
			return false;
		}
		$files = array();
		while (false !== ($entry = $dir->read()))
		{
			if (($entry !== '.') && ($entry !== '..'))
			{
				if ($flowFile = $this->getFileFromDir($path.'/'.$entry))
				{
					$files[] = $flowFile;
				}
			}
		}
		return $files;
	}
	
	private function getFileFromDir($dir)
	{
		return GWF_File::isFile($dir.'/0') ?
			array(
				'name' => @file_get_contents($dir.'/name'),
				'mime' => @file_get_contents($dir.'/mime'),
				'size' => filesize($dir.'/0'),
				'dir' => $dir,
				'path' => $dir.'/0',
			) : false;
	}
	
	public function cleanup()
	{
		GWF_File::removeDir($this->getTempDir(), false);
	}
	
	public function onFlowUpload()
	{
		if (!isset($_REQUEST['flowIdentifier']))
		{
			return false;
		}
		foreach ($_FILES as $key => $file)
		{
			$this->onFlowUploadFile($key, $file);
		}
		die();
	}
	
	private function onFlowUploadFile($key, $file)
	{
		$chunkDir = $this->getChunkDir($key);
		if (!GWF_File::createDir($chunkDir))
		{
			header("HTTP/1.0 500 Create temp dir");
			GWF_Log::logError('Cannot create temp dir.');
			return false;
		}
		
		if (false !== ($error = $this->deniedFlowFile($key, $file)))
		{
			header("HTTP/1.0 413 DENY: $error");
			GWF_Log::logError('flow upload denied: '.$error);
			return false;
		}
	
		if (!$this->onFlowCopyChunk($key, $file))
		{
			header("HTTP/1.0 413 Copy chunk");
			GWF_Log::logError('Cannot create temp dir.');
			return false;
		}
		
		if ($_REQUEST['flowChunkNumber'] === $_REQUEST['flowTotalChunks'])
		{
			if (false !== ($error = $this->onFlowFinishFile($key, $file)))
			{
				header("HTTP/1.0 413 Merge error: $error");
				GWF_Log::logError("Cannot finish file: $error");
				return false;
			}
		}
		
		$result = json_encode(array(
			'success' => true,
		));

		echo $result;
// 		GWF_Log::logCron($result);
		return true;
	}

	private function onFlowCopyChunk($key, $file)
	{
		if (!$this->onFlowCheckSizeBeforeCopy($key, $file))
		{
			return false;
		}
		$chunkDir = $this->getChunkDir($key);
		$chunkNumber = (int) $_REQUEST['flowChunkNumber'];
		$chunkFile = $chunkDir.'/'.$chunkNumber;
		return @copy($file['tmp_name'], $chunkFile);
	}
	
	private function onFlowCheckSizeBeforeCopy($key, $file)
	{
		$chunkDir = $this->getChunkDir($key);
		$already = GWF_File::dirsize($chunkDir);
		$additive = filesize($file['tmp_name']);
		$sumSize = $already + $additive;
		$maxSize = $this->form_data[$key][self::LENGTH]['maxSize'];
		if ($sumSize > $maxSize)
		{
			$this->denyFlowFile($key, $file, "exceed size of $maxSize");
			return false;
		}
		return true;
	}
	
	private function onFlowFinishFile($key, $file)
	{
		$chunkDir = $this->getChunkDir($key);
		
		# Merge chunks to single temp file
		$finalFile = $chunkDir.'/temp';
		GWF_File::filewalker($chunkDir, array($this, 'onMergeFile'), false, true, array($finalFile));
		
		# Write user chosen name to a file for later
		$nameFile = $chunkDir.'/name';
		@file_put_contents($nameFile, $file['name']);
		
		# Write mime type for later use
		$mimeFile = $chunkDir.'/mime';
		@file_put_contents($mimeFile, mime_content_type($chunkDir.'/temp'));
		
		# Run finishing tests to deny.
		if (false !== ($error = $this->onFlowFinishTests($key, $file)))
		{
			$this->denyFlowFile($key, $file, $error);
			return $error;
		}
		
		# Move single temp to chunk 0
		if (!@rename($finalFile, $chunkDir.'/0'))
		{
			return "Cannot move temp file.";
		}
		
		return false;
	}
	
	public function onMergeFile($entry, $fullpath, $args)
	{
		list($finalFile) = $args;
		@file_put_contents($finalFile, file_get_contents($fullpath), FILE_APPEND);
	}
	
	private function onFlowFinishTests($key, $file)
	{
		if (false !== ($error = $this->onFlowTestChecksum($key, $file)))
		{
			return $error;
		}
		if (false !== ($error = $this->onFlowTestMime($key, $file)))
		{
			return $error;
		}
		if (false !== ($error = $this->onFlowTestImageDimension($key, $file)))
		{
			return $error;
		}
		return false;
	}
	
	private function onFlowTestChecksum($key, $file)
	{
		return false;
	}
	
	private function onFlowTestMime($key, $file)
	{
		if (!isset($this->form_data[$key][self::LENGTH])) {
			return "$key has no fourth form param for mimetest";
		}
		if (!isset($this->form_data[$key][self::LENGTH]['mimeTypes'])) {
			return "$key has no fourth form param named 'mimeTypes' for mimetest";
		}
		$mimes = $this->form_data[$key][self::LENGTH]['mimeTypes'];
		if (!($mime = @file_get_contents($this->getChunkDir($key).'/mime'))) {
			return "$key: No mime found for file";
		}
		if (!in_array($mime, $mimes, true)) {
			return "$key: Unsupported MIME TYPE: $mime";
		}
		return false;
	}
	
	private function onFlowTestImageDimension($key, $file)
	{
		return false;
	}
	
}

