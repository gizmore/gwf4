<div class="gwf-login-form"><?php echo $form; ?></div>

<section class="gwf-button-bar" layout="row" layout-sm="column" layout-align="center center" layout-wrap>
<?php
$buttons = '';
if ($register) $buttons .= GWF_Button::generic($lang->lang('btn_register'), "$root/register");
if ($recovery) $buttons .= GWF_Button::generic($lang->lang('btn_recovery'), "$root/recovery");
if ($facebookUrl) $buttons .= GWF_Button::generic($lang->lang('btn_fb_login'), $facebookUrl);
echo $buttons;
?>
</section>
