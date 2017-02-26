<gwf-sidebar-item>
<ul>
<?php
foreach ($modules as $module)
{
	$module instanceof GWF_Module;
	printf('<li><a href=""></a></li>', $module->getName());
}
?>
</ul>
</gwf-sidebar-item>
