<?php
$required = " required";
$input = null;
$no_head = array(GWF_Form::HIDDEN, GWF_Form::SUBMIT);
?>

<section layout="column" flex>
	<form class="gwf-form-x" action="<?php echo $action; ?>" method="<?php echo $method; ?>" enctype="<?php echo $enctype; ?>">
		<h2><?php echo $title; ?></h2>

<?php
foreach ($tVars['data'] as $key => $data)
{
	if (!in_array($data[0], $no_head, true))
	{
		$label = isset($data[2]) ? $data[2] : '';
		printf('<gwf-label>%s</gwf-label>', $label);
	}
}
?>
<br/>
<hr/>
<br/>
<?php
foreach ($tVars['data'] as $key => $data)
{
	echo '<span>';
	switch ($data[0])
	{
		case GWF_Form::HIDDEN:
			printf('<input type="hidden" name="%s" value="%s" />'.PHP_EOL, $key, $data[1]);
			break;
		case GWF_Form::INT:
		case GWF_Form::FLOAT:
		case GWF_Form::STRING:
		case GWF_Form::STRING_NO_CHECK:
			printf('<input type="text" name="%s" value="%s" />'.PHP_EOL, $key, $data[1]);
			break;
		case GWF_Form::PASSWORD:
			printf('<input type="password" name="%s" value="" />'.PHP_EOL, $key);
			break;
		case GWF_Form::CHECKBOX:
			printf('<input type="checkbox" name="%s"%s value="" />'.PHP_EOL, $key, ($data[1]?' checked="checked"':''));
			break;
		case GWF_Form::SUBMIT:
			printf('<input type="submit" name="%s" value="%s" />'.PHP_EOL, $key, $data[1]);
			break;
		case GWF_Form::TIME:
		case GWF_Form::DATE:
		case GWF_Form::SELECT:
		case GWF_Form::SSTRING:
		case GWF_Form::HTML:
			printf('%s'.PHP_EOL, $data[1]);
			break;
			
//		case GWF_Form::CAPTCHA:
//			printf('<tr><td>%s</td><td>%s</td><td><img src="%sCaptcha/" onclick="this.src=\'%sCaptcha/?\'+(new Date()).getTime();" /></td></tr>'.PHP_EOL, GWF_HTML::lang('th_captcha1'), GWF_Button::tooltip(GWF_HTML::lang('tt_captcha1')), GWF_WEB_ROOT, GWF_WEB_ROOT);
//			printf('<tr><td>%s</td><td>%s</td><td><input type="text" name="%s" value="%s" /></td></tr>'.PHP_EOL, GWF_HTML::lang('th_captcha2'), GWF_Button::tooltip(GWF_HTML::lang('tt_captcha2')), $key, $data[1]);
//			break;
			
		default:
			GWF4::logDie(sprintf('Your '.__FILE__.' is missing datatype %d', $data[0]));
		
	}
	echo '</span>'.PHP_EOL;
}
?>
	</form>
</section>
