<gwf-sidebar-item>
<?php
if (GWF_User::isLoggedIn())
{
	echo GWF_Button::generic($lang->lang('btn_logout'), $hrefLogout);
}
else
{
	echo $form;
}
?>
</gwf-sidebar-item>
