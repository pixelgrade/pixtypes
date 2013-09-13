<?php defined('ABSPATH') or die;
	/* @var PixtypesFormField $field */
	/* @var PixtypesForm $form */
	/* @var mixed $default */
	/* @var string $name */
	/* @var string $idname */
	/* @var string $label */
	/* @var string $desc */
	/* @var string $rendering */

	isset($type) or $type = 'text';

	$attrs = array
		(
			'name' => $name,
			'id' => $idname,
			'type' => 'text',
			'value' => $form->autovalue($name)
		);
?>

<?php if ($rendering == 'inline'): ?>
	<input <?php echo $field->htmlattributes($attrs) ?>/>
<?php else: # ?>
	<div>
		<p><?php echo $desc ?></p>
		<label id="<?php echo $name ?>">
			<?php echo $label ?>
			<input <?php echo $field->htmlattributes($attrs) ?>/>
		</label>
	</div>
<?php endif; ?>
