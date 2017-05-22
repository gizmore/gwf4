<?php
$user = GWF_User::getStaticOrGuest();
$module = Module_GWF::instance();
$module->onLoadLanguage();
$lang = $module->getLang();
?>
<nav class="navbar navbar-default navbar-not-fixed-top">

	<div class="container">

		<button id="gwf-sidebar-toggle-left" type="button" class="navbar-toggle" data-toggle="sidebar" data-target=".sidebar-left">
			<span class="sr-only">Left</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<button id="gwf-sidebar-toggle-right" type="button" class="navbar-toggle" data-toggle="sidebar" data-target=".sidebar-right">
			<span class="sr-only">Right</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>

		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?php echo GWF_WEB_ROOT; ?>"><?php echo GWF_SITENAME; ?></a>
		</div>
		<div id="navbar" class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<li><a href="<?php echo GWF_WEB_ROOT; ?>"><?php echo $lang->lang('btn_home'); ?></a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
<?php if ($user->isAdmin()) { ?>
				<li><a href="/nanny">Admin</a></li>
<?php } ?>
<?php if ($user->isGuest()) { ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Authenticate<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="/login"><?php echo $lang->lang('btn_login'); ?></a></li>
						<li><a href="/register"><?php echo $lang->lang('btn_register'); ?></a></li>
					</ul>
				</li>
<?php } else { ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $user->displayName(); ?><span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="/account"><?php echo $lang->lang('btn_account'); ?></a></li>
						<li><a href="/logout"><?php echo $lang->lang('btn_logout'); ?></a></li>
					</ul>
				</li>
<?php } ?>
			</ul>
		</div><!--/.nav-collapse -->
	</div>
</nav>
