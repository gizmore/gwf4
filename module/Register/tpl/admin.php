<?php
$headers = array(
	array($lang->lang('th_username'), 'username', 'ASC'),
	array($lang->lang('th_token'), 'token', 'ASC'),
	array($lang->lang('th_email'), 'email', 'ASC'),
	array($lang->lang('th_birthdate'), 'birthdate', 'ASC'),
	array($lang->lang('th_countryid'), 'countryid', 'ASC'),
	array($lang->lang('th_timestamp'), 'timestamp', 'ASC'),
	array($lang->lang('th_ip'), 'ip', 'ASC'),
);

$data = array();
foreach ($tVars['activations'] as $ua)
{
	$ua instanceof GWF_UserActivation;
	$data[] = array(
		$ua->display('username'),
		$ua->getVar('token'),
		$ua->display('email'),
		$ua->displayBirthdate(),
		$ua->displayCountry(),
		$ua->displayTimestamp(),
		$ua->displayIP(),
	);
}

echo $tVars['pagemenu'];

echo GWF_Table::display($headers, $tVars['sort_url'], $data, $tVars['by'], $tVars['dir']);
?>
