<?php defined('ABSPATH') or die;
	/* @var $form PixtypesForm */
	/* @var $conf PixtypesMeta */

	/* @var $f PixtypesForm */
	$f = &$form;
?>

<?php foreach ($conf->get('fields', array()) as $fieldname): ?>

	<?php
	$field_markup = $f->field($fieldname)
		->addmeta('special_sekrit_property', '!!')
		->render();
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Field renderer escapes its own controls.
	echo $field_markup;
	?>

<?php endforeach; ?>
