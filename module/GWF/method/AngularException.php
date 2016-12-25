<?php
final class GWF_AngularException extends GWF_Method
{
	const APP_LOGFILE = 'angular_err';
	
	public function execute()
	{
		GWF_Log::log(self::APP_LOGFILE, $this->logMessage());
	}
	
	private function logMessage()
	{
		$timestamp = time();
		$user_id = Common::getRequestInt('user_id', '?');
		$user_name = Common::getRequestString('user_name', '?');
		$user_agent = Common::getRequestString('user_agent', '');
		$resolution = Common::getRequestString('resolution', '');
		$stacktrace = Common::getRequestString('stacktrace', '');
		$msg=<<<EOT
{$timestamp}: Angular Exception 
{$stacktrace}
------------------
{$user_name}({$user_id})@{$resolution} px
UserAgent: {$user_agent}
------------------

EOT;
	return $msg;
	}
}
