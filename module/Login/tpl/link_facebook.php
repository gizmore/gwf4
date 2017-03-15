<?php
if ($facebookURL)
{
	$content = GWF_Button::generic($lang->lang('btn_connect_to_facebook'), $facebookURL);
}
else
{
	$content = $lang->lang('err_already_connected_to_fb');
}

echo GWF_Box::box($content, $lang->lang('bt_connect_to_facebook'));
