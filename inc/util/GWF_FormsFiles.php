<?php
/**
 * File upload form field.
 * Requires angular and flow.
 * @author gizmore
 * @since 4.2
 */
class GWF_FormsFiles extends GWF_FormsField
{
	private $maxSize;
	private $maxFiles;
	private $allowedMimes;
	private $newFiles;
	
	public function getUploadedFiles() { return $this->newFiles; }
	public function getFormValue() {}
	
	public function __construct($name, $label, array $files, $minFiles=0, $maxFiles=1, $maxSize=1000000, $allowedMimes=array())
	{
		parent::__construct($name, $label, $files);
		$this->maxSize = $maxSize;
		$this->maxFiles = $maxFiles;
		$this->allowedMimes = $allowedMimes;
	}
	
	/**
	 * Render flow upload application.
	 * @see GWF_FormsField::renderInput()
	 * @return string HTML
	 */
	public function renderInput()
	{
		$tVars = array(
			'form' => $this->getForm(),
			'field' => $this,
			'maxFiles' => $this->maxFiles,
			'files' => $this->getValue(),
			'single' => $this->maxFiles < 2,
			'config' => $this->flowConfig(),
			'image' => true,
		);
		return GWF_Template::templateMain('forms_files.php', $tVars);
	}
	
	public function validate($name)
	{
		$this->newFiles = array();
		$files = $this->getValue();
		foreach ($this->getFiles($name, array()) as $file)
		{
			$files[] = $file;
			$this->newFiles[] = $file;
		}
		
		if (count($files) > $this->maxFiles)
		{
			return GWF_HTML::lang('err_files_count', array($this->maxFiles));
		}
		
		$this->setValue($files);
	}
	
	/**
	 * Get javascript config array for upload controller.
	 * @return string JSON
	 */
	private function flowConfig()
	{
		return GWF_Javascript::htmlAttributeEscapedJSON(array(
			'maxSize' => $this->maxSize,
			'maxFiles' => $this->maxFiles,
			'mimeTypes' => $this->allowedMimes
		));
	}
	
	public function onFlowUpload()
	{
		foreach ($_FILES as $key => $file)
		{
			$this->onFlowUploadFile($key, $file);
		}
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
		if ($files = $this->getFiles($key, $default))
		{
			return $files[0];
		}
	}
	
	private function getFiles($key, $default)
	{
		$path = $this->getTempDir($key);
		if (false === ($dir = @dir($path)))
		{
			return $default;
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
	
	/**
	 * Get a GWF_FilesFile from a flow upload dir.
	 * @param string $dir
	 * @return GWF_FormsFile
	 */
	private function getFileFromDir($dir)
	{
		if (!GWF_File::isFile($dir.'/0'))
		{
			return false;
		}
		$file = new GWF_FormsFile(@file_get_contents($dir.'/name'));
		$file->setMime(@file_get_contents($dir.'/mime'));
		$file->setPath($dir.'/0');
		return $file;
	}
	
	public function onCleanup()
	{
		GWF_File::removeDir($this->getTempDir(), false);
	}
	
	private function onFlowError($error)
	{
		header("HTTP/1.0 413 $error");
		GWF_Log::logError("FLOW: $error");
		echo $error;
		return false;
	}
	
	private function onFlowUploadFile($key, $file)
	{
		$chunkDir = $this->getChunkDir($key);
		if (!GWF_File::createDir($chunkDir))
		{
			return $this->onFlowError('Create temp dir');
		}
	
		if (false !== ($error = $this->deniedFlowFile($key, $file)))
		{
			return $this->onFlowError("Denied: $error");
		}
	
		if (!$this->onFlowCopyChunk($key, $file))
		{
			return $this->onFlowError("Copy chunk failed.");
		}
	
		if ($_REQUEST['flowChunkNumber'] === $_REQUEST['flowTotalChunks'])
		{
			if (false !== ($error = $this->onFlowFinishFile($key, $file)))
			{
				return $this->onFlowError($error);
			}
		}
	
		# Announce result
		$result = json_encode(array(
			'success' => true,
		));
		echo $result;
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
		$maxSize = $this->maxSize;
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
		$mimes = $this->allowedMimes;
		if (!($mime = @file_get_contents($this->getChunkDir($key).'/mime'))) {
			return "$key: No mime found for file";
		}
		if ((!in_array($mime, $mimes, true)) && (count($mimes)>0)) {
			return "$key: Unsupported MIME TYPE: $mime";
		}
		return false;
	}
	
	private function onFlowTestImageDimension($key, $file)
	{
		return false;
	}
	
	
}
