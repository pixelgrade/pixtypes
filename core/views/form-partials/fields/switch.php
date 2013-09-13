<?php defined('ABSPATH') or die;
	/* @var PixtypesFormField $field */
	/* @var PixtypesForm $form */
	/* @var mixed $default */
	/* @var string $name */
	/* @var string $idname */
	/* @var string $label */
	/* @var string $desc */

	// [!!] a switch is a checkbox that is only ever either on or off; not to
	// be confused with a fully functional checkbox which may be many values

	$checked = $form->autovalue($name, $default);

	$attrs = array
		(
			'name' => $name,
			'type' => 'checkbox',
			'id' => $idname,
			'value' => 1,
		);

	// is the checkbox checked?
	if ($checked) {
		$attrs['checked'] = 'checked';
	}

	// Label Fillins
	// -------------

	if ($field->hasmeta('label-fillins')) {
		$fillers = array();
		foreach ($field->getmeta('label-fillins', array()) as $fieldname => $conf) {
			$fillers[":$fieldname"] = $form->field($fieldname, $conf)->render();
		}

		$processed_label = strtr($label, $fillers);
	}
	else { // no fillins available
		$processed_label = $label;
	}

?>

<label for="<?php echo $idname ?>">
	<input <?php echo $field->htmlattributes($attrs) ?> />
	<?php echo $processed_label ?>
</label>