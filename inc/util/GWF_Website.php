<?php
/**
 * General HTML utility.
 * @see GWF_Button
 * @see GWF_Box
 * @since 1.0 
 * @author spaceone
 * @author gizmore
 */
final class GWF_Website
{
	private static $_page_title = GWF_SITENAME;
	private static $_page_title_pre = '';
	private static $_page_title_post = '';

	private static $_links = array();
	private static $_meta = array(
		'keywords' => array('keywords', '', 0),
		'description' => array('description', '', 0),
	);

	private static $_inline_css = '';
	private static $_output = '';
	private static $xhtml = "/>\n\t";

	private static $_javascripts = array();
	private static $_javascript_inline = '';

	const NONE=0, SCREEN=1, TTY=2, TV=3, PROJECTION=4, HANDHELP=5, _PRINT=6, BRAILLE=7, AURAL=8, ALL=9; 
	private static $_media = array('', 'screen','tty','tv','projection','handheld','print','braille','aural','all');

	public static function init($init_language=true)
	{
		if(isset($_GET['plain']))
		{
			self::plaintext();
		}
		else
		{
			header('Content-Type: text/html; charset=UTF-8');
		}
		
		if ($init_language)
		{
			GWF_Language::init();
		}
		GWF_HTML::init();

		self::$xhtml = (self::isHTML() ? '>' : ' />') . "\n\t";

		self::addLink(GWF_WEB_ROOT.'favicon.ico', 'img/x-icon', 'shortcut icon');
		self::addMeta(array('Content-Type', 'text/html; charset=utf-8', 1));
	}

	public static function plaintext() { header('Content-Type: text/plain; charset=UTF-8'); }
	public static function isAjax() { return (isset($_REQUEST['ajax'])) || (isset($_SERVER['REDIRECT_QUERY_STRING']) && (strpos($_SERVER['REDIRECT_QUERY_STRING'], 'ajax=') !== false)); }
	public static function isHTML() { return false === strpos(GWF_DEFAULT_DOCTYPE, 'xhtml'); }
	public static function getBirthdate() { return GWF_Settings::getSetting('gwf_site_birthdate', date('Ymd')); } //TODO: move to other file
	public static function getDefaultOutput() { return self::$_output; }
	public static function displayPageTitle() { return htmlspecialchars(self::$_page_title_pre.self::$_page_title.self::$_page_title_post); }
	public static function indent(&$str, $num=1) { return str_replace("\n", "\n".  str_repeat("\t", $num), $str); }

	public static function redirect($url)
	{
		if (!self::isAjax())
		{
			header('Location: ' . $url);
			echo "You should be redirected to: ".htmlspecialchars($url);
#			die();
		}
		else
		{
			return self::ajaxRedirect($url);
		}
	}
	private static function ajaxRedirect($url)
	{
		# Don't do this at home kids!
		return sprintf('<script type="text/javascript">setTimeout(function(){ window.location.href="%s" }, 1);</script>', $url);
	}
	public static function redirectMeta($url, $seconds)
	{
		header(sprintf('refresh: %d; url=%s', $seconds, $url));
	}
	public static function redirectBack()
	{
		$url = GWF_WEB_ROOT;
		if (false !== ($url2 = GWF_Session::getLastURL()))
		{
			$url = GWF_WEB_ROOT.ltrim($url2, '/');
		}
		self::redirect($url);
	}

	public static function addDefaultOutput($html) { self::$_output .= $html; }
	public static function addJavascriptInline($script_html) { self::$_javascript_inline .= $script_html;} # Raw JavaScript
	public static function addInlineCSS($css) { self::$_inline_css .= $css; }
	public static function addMetaDescr($s) { self::$_meta['description'][1] .= $s; }
	public static function addMetaTags($s) { self::$_meta['keywords'][1] .= $s; }
	public static function addFeed($href, $title, $media=0) { self::addLink($href, 'application/rss+xml', 'alternate', $media, $title); }
	public static function addCSS($path, $media=0) { self::addLink($path, 'text/css', 'stylesheet', $media); }
	public static function addBowerCSS($path) { self::addCSS(GWF_WEB_ROOT."bower_components/$path"); }
	
	public static function setMetaTags($s) { self::$_meta['keywords'][1] = $s; }
	public static function setMetaDescr($s) { self::$_meta['description'][1] = $s; }
	public static function setPageTitle($title) { self::$_page_title = $title; }
	public static function setPageTitlePre($title) { self::$_page_title_pre = $title; }
	public static function setPageTitleAfter($title) { self::$_page_title_post = $title; }
	public static function getPageTitle() { return self::$_page_title; }

	/**
	 * add an html <link>
	 * @param string $type = mime_type
	 * @param mixed $rel relationship (one
	 * @param int $media
	 * @param string $href URL
	 * @see http://www.w3schools.com/tags/tag_link.asp
	 */
	public static function addLink($href, $type, $rel, $media=0, $title='')
	{
		self::$_links[] = array(htmlspecialchars($href), $type, $rel, (int)$media, htmlspecialchars($title));
	}

	# TODO: move
	public static function parseLink($href)
	{
		$url = parse_url(urldecode($href));
		$scheme = isset($url['scheme']) ? $url['scheme'].'://' : '';
		$host = isset($url['host']) ? htmlspecialchars($url['host']) : '';
		$port = isset($url['port']) ? sprintf(':%d', $url['port']) : '';
		$path = isset($url['path']) ? htmlspecialchars($url['path']) : '';
		$querystring = '';
		if (isset($url['query']))
		{
			$querystring .= '?';
			parse_str($url['query'], $query);
			foreach ($query as $k => $v)
			{
				$k = urlencode($k);
				if (is_array($v))
				{
					foreach($v as $vk => $vv)
					{
						$querystring .= sprintf('%s[%s]=&s&', $k, is_int($vk) ? '' : urlencode($vk), urlencode($vv));
					}
				}
				else
				{
					$querystring .= sprintf('%s=%s&', $k, urlencode($v));
				}
			}
			$querystring = htmlspecialchars(substr($querystring, 0, -1));
		}
		return $scheme . $host . $port . $path . $querystring;
	}

	/**
	 * Output of {$head_links}
	 * @return string
	 */
	public static function displayLink()
	{
		$back = '';
		foreach(self::$_links as $link)
		{
			list($href, $type, $rel, $media, $title) = $link;
			$media = self::$_media[$media];
			$media = $media === '' ? '' : ' media="'.$media.'"';
			$title = $title === '' ? '' : ' title="'.$title.'"';

			$back .= sprintf('<link rel="%s" type="%s" href="%s"%s%s%s', $rel, $type, $href, $media, $title, self::$xhtml);
		}
		# embedded CSS (move?)
		if('' !== self::$_inline_css)
		{
			$back .= sprintf("\n\t<style><!--\n\t%s\n\t--></style>\n", self::indent(self::$_inline_css, 2));
		}
		return $back;
	}

	/**
	 * You can not specify media and title with this function
	 * @param array $paths
	 * @param type $pre
	 * @param type $after 
	 */
	public static function addCSSA(array $paths, $pre='', $after='')
	{
		foreach($paths as $path)
		{
			self::addCSS($pre.$path.$after);
		}
	}

	/**
	 * add an html <meta> tag
	 * @param array $meta = array($name, $content, 0=>name;1=>http-equiv);
	 * @param boolean $overwrite overwrite key if exist?
	 * @return boolean false if metakey was not overwritten, otherwise true
	 * @todo possible without key but same functionality?
	 * @todo strings as params? addMeta($name, $content, $mode, $overwrite)
	 */
	public static function addMeta(array $metaA, $overwrite=false)
	{
		if((false === $overwrite) && (isset(self::$_meta[$metaA[0]]) === true))
		{
			return false;
		}
		self::$_meta[$metaA[0]] = $metaA;
		//self::$_meta[$name] = array($name, $content, $mode);
		return true;
	}

	public static function addMetaA(array $metaA)
	{
		foreach($metaA as $meta)
		{
			self::addMeta($meta);
		}
	}

	/**
	 *
	 * @see addMeta()
	 */
	public static function displayMeta()
	{
		$back = '';
		$mode = array('name', 'http-equiv');
		foreach (self::$_meta as $meta)
		{
			if (!is_array($meta))
			{
				continue; # TODO: spaceone fix.
			}
			list($name, $content, $equiv) = $meta;
 			$equiv = $mode[$equiv];
			$back .= sprintf('<meta %s="%s" content="%s"%s', $equiv, $name, $content, self::$xhtml);
		}
		return $back;
	}

	################
	## JAVASCRIPT ##
	################
	public static function addJavascript($path)
	{
		if (false === in_array($path, self::$_javascripts, true))
		{
			self::$_javascripts[] = $path;
		}
	}
	
	public static function addBowerJavascript($path)
	{
		self::addJavascript(GWF_WEB_ROOT."bower_components/$path");
	}

	public static function displayJavascripts()
	{
		$back = '';
		$javascripts = defined('GWF_MINIFY_JS') && GWF_MINIFY_JS ? GWF_Minify::minified(self::$_javascripts) : self::$_javascripts;
		foreach ($javascripts as $js)
		{
			$back .= sprintf('<script type="text/javascript" src="%s"></script>', htmlspecialchars($js));
		}
		return $back.self::displayJavascriptInline();
	}

	public static function displayJavascriptInline()
	{
		$inline_defines = sprintf('var GWF_WEB_ROOT = \'%s\'; var GWF_DOMAIN = \'%s\';'.PHP_EOL, GWF_WEB_ROOT, GWF_DOMAIN);
		return sprintf('<script type="text/javascript">%s</script>', $inline_defines.self::displayJavascriptOnload());
	}

	private static function displayJavascriptOnload()
	{
// 		return self::$_javascript_inline ? sprintf('; $(function(){ %s; });', self::$_javascript_inline) : '';
		return self::$_javascript_inline ? sprintf('; %s;', self::$_javascript_inline) : '';
	}

	####################
	### Display Page ###
	####################
	public static function isMobile()
	{
		return false;
	}

	public static function getHTMLHead($tVars=NULL)
	{
		
		$realVars = array(
			'page_title' => self::displayPageTitle(),
			'language' => GWF_Language::getCurrentISO(),
			'meta' => self::displayMeta(),
			'js' => self::displayJavascripts(),
			'head_links' => self::displayLink(),
		);
		
		if (is_array($tVars)) {
			$realVars = array_merge($realVars, $tVars);
		}

		# Errors and messages from GWF_Error
		GWF_Error::displayAll();

		return GWF_Doctype::getDoctype(GWF_DEFAULT_DOCTYPE) . GWF_Template::templateMain('html_head.php', $realVars) . PHP_EOL;
	}

	public static function getHTMLbody_head($tVars=NULL)
	{
		return GWF_Template::templateMain('html_body.php', $tVars);
	}

	public static function getHTMLbody_foot($tVars=NULL)
	{
		return GWF_Template::templateMain('html_foot.php', $tVars);
	}

	public static function getPagehead()
	{
		return self::getHTMLHead() . self::getHTMLbody_head();
	}

	public static function getHTMLBody($page, $tVars=NULL)
	{
		return self::getHTMLbody_head($tVars) . self::getDefaultOutput() .  $page . self::getHTMLbody_foot($tVars);
	}

	public static function displayPage($page, $tVars=NULL)
	{
		return self::getHTMLHead($tVars) . self::getHTMLBody($page, $tVars) . PHP_EOL.'</html>';
	}
}
