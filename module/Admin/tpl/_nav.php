<?php
echo GWF_Button::wrapStart();
foreach ($buttons as $btn)
{
	echo GWF_Button::generic($btn[1], $btn[0], 'generic', '', $btn[2]);
}
echo GWF_Button::wrapEnd();
