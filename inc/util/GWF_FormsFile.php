<?php
/**
 * Class representing a file on the filesystem.
 * @author gizmore
 * @since 4.2
 * @see GWF_Upload
 */
final class GWF_FormsFile
{
	private $name;
	private $path;
	private $href;
	private $mime;
	private $size;
	
	/**
	 * Remove unsafe chars for a user inputted path.
	 * @param string $path
	 * @return string cleaned path
	 */
	public static function filterPath($path) { return str_replace(array('/', '\\', '$', '!', '`', '(', ')'), '', ltrim(trim($path), '.')); }
	
	public static function fromPath($path)
	{
		$name = basename($path);
		return new self($name, $path);
	}
	
	
	public function __construct($name, $path=null, $href=null)
	{
		$this->setName($name);
		$this->setPath($path);
		$this->setHref($href);
	}
	
	public function setName($name) { $this->name = $name; }
	public function setPath($path) { $this->path = $path; $this->mime = null; $this->size = null; }
	public function setHref($href) { $this->href = $href; }
	public function setMime($mime) { $this->mime = $mime; }
	public function setSize($size) { $this->size = $size; }
	
	public function getName() { return $this->name; }
	public function getPath() { return $this->path; }
	public function getHref() { return $this->href; }
	
	public function displayName() { return GWF_HTML::display($this->name); }
	public function displayPath() { return GWF_HTML::display($this->path); }
	public function displayHref() { return GWF_HTML::display($this->href); }
	
	public function getMime()
	{
		$this->mime = $this->mime ? $this->mime : mime_content_type($this->path);
		return $this->mime;
	}
	
	public function getSize()
	{
		$this->size = $this->size ? $this->size : filesize($this->path);
		return $this->size;
	}
	
	public function copyTo($newPath)
	{
		copy($this->getPath(), $newPath);
		$this->setPath($newPath);
	}
	
	public function stream()
	{
		header('Content-Type: '.$this->getMime());
		header('Content-Size: '.$this->getSize());
		GWF_Upload::stream($this->getPath());
	}
}
