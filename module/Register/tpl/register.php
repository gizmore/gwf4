<?php
if (!GWF_Session::haveCookies())
{
echo GWF_HTML::err('ERR_COOKIES_REQUIRED', NULL, false);
}
?>

<?php
echo $form;
?>

<?php
echo GWF_Button::wrapStart();
$buttons = '';
if ($login) $buttons .= GWF_Button::generic($lang->lang('btn_login'), "{$root}login");
if ($recovery) $buttons .= GWF_Button::generic($lang->lang('btn_recovery'), "{$root}recovery");
// if ($facebookUrl) $buttons .= GWF_Button::generic($lang->lang('btn_fb_login'), $facebookUrl);
echo $buttons;
echo GWF_Button::wrapEnd();
?>
