<div ng-app="gwf4">
<div ng-controller="UploadCtrl" class="gwf4-form-images" ng-init="initGWFFormConfig(<?php echo $config; ?>);">
<input type="hidden" name="<?php echo $field->getName(); ?>" value="{{$flow.files.length ? '1' : '' }}" />
<div flow-init="{target: '<?php echo $form->getAction(); ?>', singleFile: <?php echo $single ? 'true' : 'false'; ?>, fileParameterName: '<?php echo $field->getName(); ?>', testChunks: false}"
	 flow-file-progress="onFlowProgress($file, $flow, $message);"
	 flow-file-success="onFlowSuccess($file, $flow, $message);"
	 flow-file-error="onFlowError($file, $flow, $message);"
	 flow-files-submitted="onFlowSubmitted($flow);"
	 ng-init="$flow.files.length = 0;"
	>
	<div class="gwf-flow-upload-area">
	<?php if ($image) { ?>
		<?php foreach ($files as $n => $file) { ?>
		<gwf4-flow-preview>
			<button type="submit" class="remove-button" name="deleteFile[<?php echo $n+1; ?>]" class="gwf-delete-file">X</button>
			<img src="<?php echo $file->displayHref(); ?>" />
		</gwf4-flow-preview>
		<?php } ?>
		<gwf4-flow-preview ng-repeat="$flowfile in $flow.files">
			<button type="button" class="remove-button" ng-click="onRemoveFile($flowfile, $flow);">X</button>
			<img flow-img="$flowfile" />
		</gwf4-flow-preview>
		<gwf4-flow-drop flow-drop flow-btn>Drag Files</gwf4-flow-drop>
	<?php } ?>
		<div class="cb"></div>
	</div>
	
	<gwf4-progress-indicator ng-disabled="progressIndicatorDisabled();">
		<div>({{data.transfer.fileNum}} / {{data.transfer.filesCount}}) – {{data.transfer.fileName}}  @ {{data.transfer.speed|transferSpeed}}</div>
		<md-progress-linear md-mode="determinate" ng-value="progressIndicatorValue();" ng-disabled="progressIndicatorDisabled();"></md-progress-linear>
	</gwf4-progress-indicator>
</div>
</div>
</div>
