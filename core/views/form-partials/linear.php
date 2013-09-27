<?php defined('ABSPATH') or die;
	/* @var $form PixtypesForm */
	/* @var $conf PixtypesMeta */

	/* @var $f PixtypesForm */
	$f = &$form;
?>

<?php foreach ($conf->get('fields', array()) as $fieldname): ?>

	<?php echo $f->field($fieldname)
		->addmeta('special_sekrit_property', '!!')
		->render() ?>

<?php endforeach; ?>
