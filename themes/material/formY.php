<?php $required = " required"; $input = null; ?>
<gwf-form class="md-whiteframe-8dp">
	<form class="gwf4-form" action="<?php echo $action; ?>" method="<?php echo $method; ?>" enctype="<?php echo $enctype; ?>">
	<h2><?php echo $title; ?></h2>
	<?php foreach ($tVars['data'] as $key => $data) {
		$tt = '';
		if (!empty($data[3]))
		{
			$tt = sprintf('<md-tooltip md-direction="bottom">%s</md-tooltip>', $data[3]);
		}
		$label = ''; $value = isset($data[1]) ? $data[1] : null;
		if (!empty($data[2]))
		{
			$label = '<label>'.$data[2].'</label>';
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
				printf('<div><input type="hidden" name="%s" value="%s" /></div>', $key, $data[1]);
				break;
				
			case GWF_Form::STRING:
			case GWF_Form::SSTRING:
			case GWF_Form::STRING_NO_CHECK:
				$disabled = $type === GWF_Form::SSTRING ? ' disabled="disabled"' : '';
				$input = sprintf('<input name="%s" value="%s"%s />', $key, $value, $disabled);
				break;
				
			case GWF_Form::INT:
				$input = sprintf('<input type="number" name="%s" size="8" value="%s" />', $key, $value);
				break;
				
			case GWF_Form::FLOAT:
				$input = sprintf('<input type="number" name="%s" size="8" value="%s" step="0.02" />', $key, $value);
				break;
				
			case GWF_Form::PASSWORD:
				$input = sprintf('<input type="password" name="%s" value="" />', $key);
				break;
				
			case GWF_Form::CHECKBOX:
				$checked = $data[1] ? ' class="md-checked"' : '';
				$input = sprintf('<md-checkbox type="checkbox" aria-label="%s" name="%s"%s />', htmlspecialchars($label), $key, $checked);
				echo "<md-input-container class=\"gwf-checkbox\">$label $tt $input</md-input-container>";
				$input = null;
				break;
				
			case GWF_Form::CAPTCHA:
				$foo = empty($data[1]) ? '' : '/'.$data[1];
				$label = '<label>'.GWF_HTML::lang('th_captcha1').'</label>'; 
				$tt = sprintf('<md-tooltip md-direction="bottom">%s</md-tooltip>', GWF_HTML::lang('tt_captcha1'));
				$img = sprintf('<img src="%sCaptcha%s" onclick="this.src=\'%1$sCaptcha/?\'+(new Date()).getTime();" alt="Captcha" />', $root, $foo);
				echo "<md-input-container>$label$tt</md-input-container>";
				echo "<md-input-container>$img</md-input-container>";
				
				
				$tt = sprintf('<md-tooltip md-direction="bottom">%s</md-tooltip>', GWF_HTML::lang('tt_captcha2'));
				$label = GWF_HTML::lang('th_captcha2');
				$input = sprintf('<input type="text" name="%s" value="%s" />', $key, $value);
				echo "<md-input-container>$label $tt $input</md-input-container>";
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
				printf('<div class="gwf-hr"></div>');
				break;
				
			case GWF_Form::HEADLINE:
				printf('<h3%s%s>%s</h3>', $req, $tt, $label);
				break;
				
			case GWF_Form::SUBMIT:
				echo '<gwf-buttons>';
				printf('<input name="%s" class="md-button md-raised" value="%s" type="submit"></input>', $key, $value);
				echo '</gwf-buttons>';
				break;
				
			case GWF_Form::SUBMITS:
				echo '<gwf-buttons>';
				foreach ($data[1] as $key => $value)
				{
					printf('<input name="%s" class="md-button md-raised" value="%s" type="submit"></input>', $key, $value);
				}
				echo '</gwf-buttons>';
				break;
				
			case GWF_Form::MESSAGE:
				echo GWF_Message::getCodeBar($key);
			case GWF_Form::MESSAGE_NOBB:
				$input = sprintf('<textarea id="%1$s" name="%1$s" cols="80" rows="8">%2$s</textarea>', $key, $value);
				break;
				
			case GWF_Form::VALIDATOR:
				break;
				
			case GWF_Form::HTML:
				echo $data[1].PHP_EOL;
				break;

			case GWF_Form::FILE_IMAGE:
			case GWF_Form::FILE:
			case GWF_Form::FILE_OPT:
			case GWF_Form::FILE_IMAGES:
				$single = $type !== GWF_Form::FILE_IMAGES;
				$imagePreview = ($type === GWF_Form::FILE_IMAGE) || ($type === GWF_Form::FILE_IMAGES); ?>
<!-- 				<div ng-app="gwf4"> -->
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
<!-- 					</div> -->
				</div> <?php				
				break;
			default:
				var_dump($data);
				GWF4::logDie(sprintf('Your tpl/formY.php is missing datatype %d', $data[0]));
		}
		
		if ($input) {
			echo "<md-input-container class=\"$class\" layout=\"row\" flex>$label $tt $input</md-input-container>";
			$input = null;
		}
		
	} ?>
	</form>
	<?php if (isset($have_required)) { echo GWF_HTML::lang('form_required', array('*')); } ?>
</gwf-form>
