<?php defined('ABSPATH') or die;
	/* @var PixtypesFormField $field */
	/* @var PixtypesForm $form */
	/* @var mixed $default */
	/* @var string $name */
	/* @var string $idname */
	/* @var string $label */
	/* @var string $desc */
	/* @var string $rendering */

	isset($type) or $type = 'hidden';

	$attrs = array
		(
			'name' => $name,
			'id' => $idname,
			'type' => 'hidden',
			'value' => $form->autovalue($name)
		);
?>

<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- htmlattributes() escapes attribute names and values. ?>
<input <?php echo $field->htmlattributes($attrs) ?>/>
