<?php echo $form; ?>

<?php echo $form_add; ?>

<?php echo $pagemenu; ?>

<md-list>

<?php echo $headers; ?>

<?php $gid = $group->getID(); ?>
<?php foreach ($userids as $userid) { ?>
	<md-list-item>
	<?php
	$user = GWF_User::getByID($userid);
	if (!$user) {
		echo GWF_Table::column(GWF_Guest::getGuest()->displayProfileLink());
	}
	else {
		echo GWF_Table::column($user)->displayProfileLink();
	}
	echo GWF_Table::column(GWF_Button::delete( $module->getMethodURL('GroupEdit', "&rem={$userid}&gid={$gid}"), $lang->lang('btn_rem_from_group') ));
	?>
	</md-list-item>

<?php } ?>


</md-list>

<?php echo $pagemenu; ?>
