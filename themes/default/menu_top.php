<div class="GWF4_top">
	<div class="fl">
		<h1>GWF v<?php echo GWF_CORE_VERSION; ?></h1>
	</div>
	<div class="fl">
		<p><?php echo GWF_Notice::getOnlineUsers(); ?></p>
	</div>
	<div class="cl"></div>
</div>

<div class="GWF4_topmenu">
	<ul>
		<!-- BOTH -->
		<li><a href="<?php echo $root; ?>news">News<?php echo GWF_Notice::getUnreadNews($user); ?></a></li>
		<li><a href="<?php echo $root; ?>about_gwf">About</a></li>
		
		<!-- BOTH -->
		<?php if ($user->isLoggedIn()) { ?>
		<li><a href="<?php echo $root; ?>links">Links<?php echo GWF_Notice::getUnreadLinks($user, '[%s]', ''); ?></a></li>
		<li><a href="<?php echo $root; ?>forum">Forum<?php echo GWF_Notice::getUnreadForum($user, '[%s]', ''); ?></a></li>
		<li><a href="<?php echo $root; ?>irc_chat">Chat</a></li>
		<li><a href="<?php echo $root; ?>pm">PM<?php echo GWF_Notice::getUnreadPM($user, '[%s]', ''); ?></a></li>
		<li><a href="<?php echo $root; ?>account">Account</a></li>
		<li><a href="<?php echo $root; ?>profile_settings">Profile</a></li>
		<?php if ($user->isAdmin()) { ?>
		<li><a href="<?php echo $root; ?>nanny">Admin</a></li>
		<li><a href="<?php echo $root; ?>index.php?mo=PageBuilder&me=Admin">CMS</a></li>
		<?php } ?>
		<li><a href="<?php echo $root; ?>logout">Logout</a>[<a href="<?php echo $root; ?>account"><?php echo $user->display('user_name'); ?></a>]</li>
		<?php } else { ?>

		<!-- GUEST -->
		<li><a href="<?php echo $root; ?>links">Links</a></li>
		<li><a href="<?php echo $root; ?>forum">Forum</a></li>
		<li><a href="<?php echo $root; ?>irc_chat">Chat</a></li>
		<li><a href="<?php echo $root; ?>register">Register</a></li>
		<li><a href="<?php echo $root; ?>login">Login</a></li>
		<?php } ?>
	</ul>
</div>
