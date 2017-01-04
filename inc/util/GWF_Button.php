<?php
/**
 * Non-Form Buttons and Tooltips.
 * Basically a wrapper for the correct css and markup, for often used buttons.
 * @author gizmore
 * @version 1.0
 */
final class GWF_Button
{
	private static $templateButtons = true;

	/**
	 * Preload button template.
	 */
	public static function init()
	{
		if (self::$templateButtons === true)
		{
			self::$templateButtons = GWF_Template::templateMain('buttons.php');
		}
	}

	/**
	 * Get a GWF HTML button. Type is 'generic'. Command is ''. 
	 * @param string $text
	 * @param string $href
	 * @param string $type
	 * @param string $command
	 * @param boolean $selected
	 * @param string $onclick
	 * @return string
	 */
	public static function generic($text, $href='#', $type='generic', $command='', $selected=false, $onclick='')
	{
		$class = $selected ? ' gwf_btn_sel' : '';
		$onclick = $onclick === '' ? '' : " onclick=\"$onclick\"";
		return str_replace(
			array('%TEXT%', '%HREF%', '%TYPE%', '%CMD%', '%CLASS%', '%ONCLICK%'),
			array(GWF_HTML::display($text), GWF_HTML::display($href), $type, $command, $class, $onclick),
			self::$templateButtons
		);
	}


	public static function icon($class)
	{
		return sprintf('<span class="gwf_button"><span class="gwf_btn_%s"></span></span>', $class);
	}

	public static function imgbtn($class, $href, $text)
	{
		$href = GWF_HTML::display($href);
		$text = GWF_HTML::display($text);
		return sprintf('<gwf-button class="image-button"><a href="%s"><i class="material-icons">%s</i>%s</a></gwf-button>', $href, $class, $text);
	}

	public static function add($href, $text='') { return self::imgbtn('add', $href, $text); }
	public static function sub($href, $text='') { return self::imgbtn('sub', $href, $text); }
	public static function favorite($href, $text='') { return self::imgbtn('favorite', $href, $text); }
	public static function mail($href, $text='') { return self::imgbtn('mail', $href, $text); }
	public static function user($href, $text='') { return self::imgbtn('user', $href, $text); }
	public static function edit($href, $text='') { return self::imgbtn('create', $href, $text); }
	public static function delete($href, $text='') { return self::imgbtn('delete', $href, $text); }
	public static function ignore($href, $text='') { return self::imgbtn('ignore', $href, $text); }
	public static function restore($href, $text='') { return self::imgbtn('restore', $href, $text); }
	public static function options($href, $text='') { return self::imgbtn('options', $href, $text); }
	public static function reply($href, $text='') { return self::imgbtn('reply', $href, $text); }
	public static function quote($href, $text='') { return self::imgbtn('quote', $href, $text); }
	public static function forward($href, $text='') { return self::imgbtn('forward', $href, $text); }
	public static function search($href, $text='') { return self::imgbtn('search', $href, $text); }
	public static function trashcan($href, $text='') { return self::imgbtn('trashcan', $href, $text); }
	public static function bell($href, $text='') { return self::imgbtn('bell', $href, $text); }
	public static function translate($href, $text='') { return self::imgbtn('translate', $href, $text); }
	public static function thumbsUp($href, $text='') { return self::imgbtn('thumbsup', $href, $text); }
	public static function thumbsDown($href, $text='') { return self::imgbtn('thumbsdown', $href, $text); }
	public static function thankYou($href, $text='') { return self::imgbtn('thanks', $href, $text); }
	public static function link($href, $text='') { return self::imgbtn('link', $href, $text); }
	public static function checkmark($enabled=true, $text='', $href='#', $command='')
	{
		return $enabled ? self::generic($text, $href, 'on', $command) : self::generic($text, $href, 'off', $command);
	}

	public static function next($href, $text='') { return self::imgbtn('next', $href, $text); }
	public static function prev($href, $text='') { return self::imgbtn('prev', $href, $text); }


	public static function tooltip($text)
	{
		return sprintf('<md-tooltip md-direction="bottom">%s</md-tooltip>', $text);
	}

	public static function up($href)
	{
		return self::generic('^', $href, 'up');
	}

	public static function down($href)
	{
		return self::generic('v', $href, 'down');
	}

	public static function wrapStart($class='')
	{
		return '<div class="gwf_buttons_outer"><div class="gwf_buttons '.$class.'">'.PHP_EOL;
	}

	public static function wrapEnd()
	{
		return '</div></div>'.PHP_EOL;
	}

	public static function wrap($html, $class='')
	{
		return self::wrapStart($class).$html.self::wrapEnd();
	}
}

GWF_Button::init();

