<div class="gwf-login-form"><?php echo $form; ?></div>

<?php
echo GWF_Button::wrapStart();
$buttons = '';
if ($register) $buttons .= GWF_Button::generic($lang->lang('btn_register'), "{$root}register");
if ($recovery) $buttons .= GWF_Button::generic($lang->lang('btn_recovery'), "{$root}recovery");
if ($facebookUrl) $buttons .= GWF_Button::generic($lang->lang('btn_fb_login'), $facebookUrl);
if ($googleUrl) $buttons .= GWF_Button::generic($lang->lang('btn_gp_login'), $googleUrl);
echo $buttons;
echo GWF_Button::wrapEnd();
