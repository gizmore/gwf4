<div>
<?php
$module = $tVars['cfgmodule'];
if (false !== ($error = GWF_ModuleLoader::checkModuleDependencies($module))) {
	echo $error;
}
$methods = GWF_ModuleLoader::getAllMethods($module);
if (count($methods) > 0)
{
	printf('<div class="box box_c">%s</div>', $lang->lang('info_methods', array(count($methods))));
	foreach ($methods as $method)
	{
		$method instanceof GWF_Method;
		if (false !== ($error = $method->checkDependencies())) {
			echo $error;
		}
	}
}
?>
<?php
if ('cfgg_info' !== ($general = $module->lang('cfgg_info'))) {
	echo sprintf('<div>%s</div>', $general);
}
?>
<?php echo $tVars['form']; ?>
<?php echo $tVars['form_install']; ?>
</div>
