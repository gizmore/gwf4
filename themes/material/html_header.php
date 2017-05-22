<div layout="column" layout-fill>
	<md-toolbar>
		<div class="md-toolbar-tools">
			<md-button ng-click="toggleLeftMenu()">Left Bar Button</md-button>
			<span flex><?php echo GWF_SITENAME; ?></span>
			<md-button ng-click="toggleRightMenu()">Right Bar Button</md-button>
		</div>
	</md-toolbar>
	<md-content ng-html="data.topContent" class="gwf-top-content"></md-content>
</div>
