
<div class="gwf-login-form"><?php echo $form; ?></div>

<?php if ($register || $recovery) { ?>
<section class="gwf-button-bar" layout="row" layout-sm="column" layout-align="center center" layout-wrap>
<?php if ($register) { ?>
	<md-button href="<?php echo $root.'register'?>"><?php echo $lang->lang('btn_register'); ?></md-button>
<?php } else if ($recovery) { ?>
	<md-button href="<?php echo $root.'recovery'?>"><?php echo $lang->lang('btn_recovery'); ?></md-button>
<?php }} ?>
</section>
