<?php
echo $form;

echo GWF_Button::wrapStart();
echo GWF_Button::generic($lang->lang('btn_login_as2', array( $user->displayUsername())), $href_login_as);
echo GWF_Button::generic($lang->lang('btn_user_groups', array( $user->displayUsername())), $href_user_groups);
echo GWF_Button::wrapEnd();
