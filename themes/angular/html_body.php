<?php
$sidebars = Module_GWF::instance()->sidebarContents('top,left,right');
?>
<body ng-app="gwf4">

	<header id="gwf4-header"><?php include 'html_header.php';?></header>
	
	<gwf-left-sidebar><?php echo $sidebars['left']; ?></gwf-left-sidebar>

	<gwf-right-sidebar><?php echo $sidebars['right']; ?></gwf-right-sidebar>

	<section layout="row" flex class="gwf-main-section">

		<div id="gwf-page-content" class="gwf-dynamic-content gwf-main-content">
