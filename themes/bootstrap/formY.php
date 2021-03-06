<?php $required = " required"; $input = null; ?>
<gwf-form class="container">
	<form class="gwf4-form" action="<?php echo $action; ?>" method="<?php echo $method; ?>" enctype="<?php echo $enctype; ?>">
	<h2><?php echo $title; ?></h2>
	<?php foreach ($tVars['data'] as $key => $data) {
		echo '<div class="form-group form-inline group-'.$key.'">';
		$tt = '';
		if (!empty($data[3]))
		{
			$tt = GWF_Button::tooltip($data[3]);
		}
		$label = ''; $value = isset($data[1]) ? $data[1] : null;
		if (!empty($data[2]))
		{
			$label = sprintf('<label for="%s">%s</label>', $key, $data[2]);
		}
		
		#$data[4] === LEN??
		$req = '';
		if (isset($data[5]) && $data[5]) {
			$have_required = true;
			$req = $required;
		}
		
		$class = '';

		$type = $data[0];
		switch ($type)
		{
			case GWF_Form::HIDDEN:
				printf('<input type="hidden" name="%s" value="%s" />', $key, $data[1]);
				break;
				
			case GWF_Form::STRING:
			case GWF_Form::SSTRING:
			case GWF_Form::STRING_NO_CHECK:
				$disabled = $type === GWF_Form::SSTRING ? ' disabled="disabled"' : '';
				$input = sprintf('<input name="%s" class="form-control" value="%s"%s />', $key, $value, $disabled);
				break;
				
			case GWF_Form::INT:
				$input = sprintf('<input type="number" class="form-control" name="%s" size="8" value="%s" />', $key, $value);
				break;
				
			case GWF_Form::FLOAT:
				$input = sprintf('<input type="number" class="form-control" name="%s" size="8" value="%s" step="0.02" />', $key, $value);
				break;
				
			case GWF_Form::PASSWORD:
				$input = sprintf('<input type="password" class="form-control" name="%s" value="" />', $key);
				break;
				
			case GWF_Form::CHECKBOX:
				$checked = $data[1] ? ' checked="checked"' : '';
				$input = sprintf('<input type="checkbox" name="%s"%s />', $key, $checked);
// 				printf('<div class="checkbox">%s%s%s</div>', $label, $tt, $input);
// 				$input = null;
				break;
				
			case GWF_Form::CAPTCHA:
				$foo = empty($data[1]) ? '' : '/'.$data[1];
				$label = '<label>'.GWF_HTML::lang('th_captcha1').'</label>'; 
				$tt = GWF_Button::tooltip(GWF_HTML::lang('tt_captcha1'));
				$img = sprintf('<img src="%sCaptcha%s" onclick="this.src=\'%1$sCaptcha/?\'+(new Date()).getTime();" alt="Captcha" />', $root, $foo);
				echo "<div>$label$tt</div>";
				echo "<div>$img</div>";
				
				$tt = GWF_Button::tooltip(GWF_HTML::lang('tt_captcha2'));
				$label = GWF_HTML::lang('th_captcha2');
				$input = sprintf('<input type="text" name="%s" value="%s" />', $key, $value);
				echo "<div>$label $tt $input</div>";
				$input = null;
				break;
				
			case GWF_Form::ENUM:
			case GWF_Form::TIME:
			case GWF_Form::DATE:
			case GWF_Form::DATE_FUTURE:
			case GWF_Form::SELECT:
			case GWF_Form::SELECT_A:
				$input = $value;
				break;
				
			case GWF_Form::DIVIDER:
				printf('<hr/>');
				$input = null;
				break;
				
			case GWF_Form::HEADLINE:
				printf('<hr/>');
				printf('<h3%s%s>%s</h3>', $req, $tt, $label);
				printf('<hr/>');
				$input = null;
				break;
				
			case GWF_Form::SUBMIT:
				echo '<gwf-button-bar>';
				printf('<input name="%s" value="%s" type="submit" class="btn btn-default" />', $key, $value);
				echo '</gwf-button-bar>';
				break;
				
			case GWF_Form::SUBMITS:
				echo '<gwf-button-bar>';
				foreach ($data[1] as $key => $value)
				{
					printf('<input name="%s" value="%s" type="submit" class="btn btn-default" />', $key, $value);
				}
				echo '</gwf-button-bar>';
				break;

			case GWF_Form::MESSAGE:
			case GWF_Form::MESSAGE_NOBB:
				$codebar = $type === GWF_Form::MESSAGE ? GWF_Message::getCodeBar($key) : '';
				$input = sprintf('<gwf-label>%s%s</gwf-label>%s<textarea id="%4$s" name="%4$s" class="form-control">%5$s</textarea>', $label, $tt, $codebar, $key, $value);
				$label = $tt = '';
				break;
				
			case GWF_Form::VALIDATOR:
				break;

			case GWF_Form::FILE_IMAGE:
			case GWF_Form::FILE:
			case GWF_Form::FILE_OPT:
			case GWF_Form::FILE_IMAGES:
				$single = $type !== GWF_Form::FILE_IMAGES;
				$imagePreview = ($type === GWF_Form::FILE_IMAGE) || ($type === GWF_Form::FILE_IMAGES); ?>
				<div ng-app="gwf4">
					<div ng-controller="UploadCtrl" class="gwf4-form-images" ng-init="initGWFFormConfig(<?php echo GWF_Javascript::htmlAttributeEscapedJSON($data[4]); ?>);">
						<input type="hidden" name="<?php echo $key; ?>" value="{{$flow.files.length ? '1' : '' }}" />
						<div flow-init="{target: '<?php echo $action?>', singleFile: <?php echo $single ? 'true' : 'false'; ?>, fileParameterName: '<?php echo $key; ?>', testChunks: false}"
							 flow-file-progress="onFlowProgress($file, $flow, $message);"
							 flow-file-success="onFlowSuccess($file, $flow, $message);"
							 flow-file-error="onFlowError($file, $flow, $message);"
							 flow-files-submitted="onFlowSubmitted($flow);"
							 ng-init="$flow.files.length = 0;">
							<div><label><?php echo $label; ?></label></div>
							<?php if ($imagePreview) { ?>
							<gwf4-flow-preview ng-repeat="$flowfile in $flow.files">
								<img flow-img="$flowfile" />
							</gwf4-flow-preview>
							<?php } ?>
							<gwf4-flow-drop flow-drop flow-btn>Drag Files</gwf4-flow-drop>
							<div class="cb"></div>
							<gwf4-progress-indicator ng-disabled="progressIndicatorDisabled();">
								<div>({{data.transfer.fileNum}} / {{data.transfer.filesCount}}) – {{data.transfer.fileName}}  @ {{data.transfer.speed|transferSpeed}}</div>
								<md-progress-linear md-mode="determinate" ng-value="progressIndicatorValue();" ng-disabled="progressIndicatorDisabled();"></md-progress-linear>
							</gwf4-progress-indicator>
						</div>
					</div>
				</div><?php				
				break;
			
			case GWF_Form::HTML:
				echo $data[1].PHP_EOL;
				break;

			default:
				var_dump($data);
				GWF4::logDie(sprintf('Your tpl/formY.php is missing datatype %d', $data[0]));
		}
		
		if ($input) {
			echo "<div class=\"row\"><div class=\"col-md-4\">$label $tt</div><div class=\"col-md-8\">$input</div></div>";
			$input = null;
		}
		
		echo '</div>';
	} ?>
	</form>
	<?php if (isset($have_required)) { echo GWF_HTML::lang('form_required', array('*')); } ?>
</gwf-form>
