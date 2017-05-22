<?php $form instanceof GWF_Forms; ?>
<div class="gwf4-forms container">
	<form method="<?php echo $form->getMethod(); ?>" enctype="<?php echo $form->getEncoding(); ?>">
		
		<h4><?php echo $form->getTitle(); ?></h4>
	
		<?php
		foreach ($form->fields() as $field)
		{
			$field instanceof GWF_FormsField;
			printf('<div class="form-group row">%s</div>', $field->render());
		}
		?>
	</form>
</div>
