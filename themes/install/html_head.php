<html>
<head>
	<title><?php echo $page_title; ?></title>

	<?php echo $meta; ?>

	<link rel="stylesheet" type="text/css" href="<?php echo GWF_WEB_ROOT; ?>module/GWF/css/gwf4.css" />
	<style type="text/css"><!--
		li#step<?php echo $step; ?> { background-color: #0000ff!important; }
	--></style>
	
	<script type="text/javascript" src="<?php echo GWF_WEB_ROOT; ?>bower_components/jquery/dist/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo GWF_WEB_ROOT; ?>themes/install/js/gwf-install.js"></script>

	<?php echo $js; ?>

	<?php echo $head_links; ?>

</head>
