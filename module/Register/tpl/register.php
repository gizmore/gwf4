<?php
if (!GWF_Session::haveCookies())
{
echo GWF_HTML::err('ERR_COOKIES_REQUIRED', NULL, false);
}
?>

<?php
echo $form;
?>


<section class="gwf-button-bar" layout="row" layout-sm="column" layout-align="center center" layout-wrap>
<?php
$buttons = '';
if ($login) $buttons .= GWF_Button::generic($lang->lang('btn_login'), "{$root}login");
if ($recovery) $buttons .= GWF_Button::generic($lang->lang('btn_recovery'), "{$root}recovery");
#if ($facebookUrl) $buttons .= GWF_Button::generic($lang->lang('btn_fb_login'), $facebookUrl);
echo $buttons;
?>
</section>
