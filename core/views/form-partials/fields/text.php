<?php defined('ABSPATH') or die;
	/* @var PixtypesFormField $field */
	/* @var PixtypesForm $form */
	/* @var mixed $default */
	/* @var string $name */
	/* @var string $idname */
	/* @var string $label */
	/* @var string $desc */

	isset($type) or $type = 'text';

	$attrs = array
		(
			'type' => 'text',
			'value' => $form->autovalue($name)
		);
?>

<div>
	<p><?php echo $desc ?></p>
	<label id="<?php echo $name ?>">
		<?php echo $label ?>
		<input <?php echo $field->htmlattributes($attrs) ?>/>
	</label>
</div>
