<gwf4-form-select ng-controller="SelectCtrl" ng-init="initSelectData(<?php echo $angularKeys; ?>, <?php echo $angularValues; ?>, '<?php echo $selected; ?>');">
	<md-input-container>
		<label><?php echo $label; ?></label>
		<md-select ng-model="data.selected" name="<?php echo $name; ?>">
			<md-option ng-repeat="key in data.keys track by $index" value="{{key}}">{{data.values[$index]}}</md-option>
		</md-select>
	</md-input-container>
</gwf4-form-select>
