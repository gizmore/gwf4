<section class="gwf-button-bar" layout="row" layout-sm="column" layout-align="center center" layout-wrap>
	<?php foreach ($buttons as $btn) { ?>
	<md-button href="<?php echo $btn[0] ?>" ng-class="{'gwf-nav-selected': $btn[2]}"><?php echo $btn[1]; ?></md-button>
	<?php } ?>
</section>
