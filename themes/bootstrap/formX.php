<?php
$required = " required";
$input = null;
$no_head = array(GWF_Form::HIDDEN, GWF_Form::SUBMIT);
?>
<gwf-form class="container">
	<form class="gwf4-form gwf-form-x" action="<?php echo $action; ?>" method="<?php echo $method; ?>" enctype="<?php echo $enctype; ?>">
		<h2><?php echo $title; ?></h2>
		<div class="row">
<?php
foreach ($tVars['data'] as $key => $data)
{
	if (!in_array($data[0], $no_head, true))
	{
		$label = isset($data[2]) ? $data[2] : '';
		printf('<label class="col-md-2" for="%s">%s</label>', $key, $label);
	}
}
?>
		</div>
		<div class="row">
<?php
foreach ($tVars['data'] as $key => $data)
{
	echo '<div class="col-md-2">';
	switch ($data[0])
	{
		case GWF_Form::HIDDEN:
			$input = sprintf('<input type="hidden" name="%s" value="%s" />'.PHP_EOL, $key, $data[1]);
			break;
		case GWF_Form::INT:
		case GWF_Form::FLOAT:
		case GWF_Form::STRING:
		case GWF_Form::STRING_NO_CHECK:
			$input = sprintf('<input type="text" name="%s" value="%s" />'.PHP_EOL, $key, $data[1]);
			break;
		case GWF_Form::PASSWORD:
			$input = sprintf('<input type="password" name="%s" value="" />'.PHP_EOL, $key);
			break;
		case GWF_Form::CHECKBOX:
			$input = sprintf('<input type="checkbox" name="%s"%s value="" />'.PHP_EOL, $key, ($data[1]?' checked="checked"':''));
			break;
		case GWF_Form::SUBMIT:
			$input = sprintf('<input type="submit" name="%s" value="%s" />'.PHP_EOL, $key, $data[1]);
			break;
		case GWF_Form::TIME:
		case GWF_Form::DATE:
		case GWF_Form::SELECT:
		case GWF_Form::SSTRING:
		case GWF_Form::HTML:
			$input = sprintf('%s'.PHP_EOL, $data[1]);
			break;
//		case GWF_Form::CAPTCHA:
//			printf('<tr><td>%s</td><td>%s</td><td><img src="%sCaptcha/" onclick="this.src=\'%sCaptcha/?\'+(new Date()).getTime();" /></td></tr>'.PHP_EOL, GWF_HTML::lang('th_captcha1'), GWF_Button::tooltip(GWF_HTML::lang('tt_captcha1')), GWF_WEB_ROOT, GWF_WEB_ROOT);
//			printf('<tr><td>%s</td><td>%s</td><td><input type="text" name="%s" value="%s" /></td></tr>'.PHP_EOL, GWF_HTML::lang('th_captcha2'), GWF_Button::tooltip(GWF_HTML::lang('tt_captcha2')), $key, $data[1]);
//			break;
			
		default:
			var_dump($data);
			GWF4::logDie(sprintf('Your '.__FILE__.' is missing datatype %d', $data[0]));
			break;
	}
	echo $input;
	echo '</div>'.PHP_EOL;
	
}
?>
		</div>
	</form>
</section>
