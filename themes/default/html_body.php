<body ng-controller="GWFCtrl">

	<header id="gwf4-header"><?php include 'html_header.php';?></header>

	<section layout="row" flex class="gwf-main-section">

		<!-- TOP -->

		<!-- LEFT -->

		<md-sidenav class="md-sidenav-left" md-component-id="left" md-disable-backdrop md-whiteframe="4">
			<div class="tgc-sidebar-menu-toggles">
				<md-button ng-click="toggleLeftMenu()" class="tgc-left-toggle md-raised" aria-label="Close Left Menu"><i class="material-icons">account_circle</i></md-button>
			</div>
			<md-toolbar class="md-theme-indigo">
				<md-content class="gwf-left-content" ng-html="data.leftContent"></md-content>
			</md-toolbar>
		</md-sidenav>

		<!-- RIGHT -->

		<md-sidenav class="md-sidenav-right" md-component-id="right" md-disable-backdrop md-whiteframe="4">
			<div class="tgc-sidebar-menu-toggles">
				<md-button ng-click="toggleRightMenu()" class="tgc-right-toggle md-raised" aria-label="Close Right Menu"><i class="material-icons">android</i></md-button>
			</div>
			<md-toolbar class="md-theme-indigo">
				<md-content class="gwf-right-content" ng-html="data.rightContent"></md-content>
			</md-toolbar>
		</md-sidenav>

		<!-- CONTENT -->
		
		<ui-view>
