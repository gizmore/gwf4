<gwf4-form-select ng-controller="SelectCtrl" ng-init="initSelectData(<?php echo $angularOptions; ?>, '<?php echo $selected; ?>');">
	<md-input-container>
		<label><?php echo $label; ?></label>
		<md-select ng-model="data.selected" name="<?php echo $name; ?>">
			<md-option ng-repeat="(value, label) in data.items" value="{{value}}">{{label}}</md-option>
		</md-select>
	</md-input-container>
</gwf4-form-select>
