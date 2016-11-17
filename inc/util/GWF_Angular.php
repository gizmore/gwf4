<?php
final class GWF_Angular
{
	private static $HEADER = '';
	private static $LEFT_SIDEBAR = '';
	private static $RIGHT_SIDEBAR = '';
	
	public static function addToLeftSidebar($content)
	{
		self::$LEFT_SIDEBAR .= $content;
	}
	
	public static function addToRightSidebar($content)
	{
		self::$RIGHT_SIDEBAR .= $content;
	}
	
	public static function leftSidebar()
	{
		return self::$LEFT_SIDEBAR;
	}
	
	public static function rightSidebar()
	{
		return self::$RIGHT_SIDEBAR;
	}

}
