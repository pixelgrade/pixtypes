<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   PixTypes
 * @author    Pixelgrade <contact@pixelgrade.com>
 * @license   GPL-2.0+
 * @link      http://pixelgrade.com
 * @copyright 2013 Pixelgrade Media
 */

$config = include pixtypes::pluginpath().'plugin-config'.EXT;

// invoke processor
$processor = pixtypes::processor($config);
$status = $processor->status();
$errors = $processor->errors();
?>

<style>

		/*
			THIS IS JUST FOR THE EXAMPLE
			Please do not place styles like this.
		*/

	.field-error {
		color: red  !important;
	}
	input[type="number"].field-error {
		border-color: lightcoral !important;
	}

</style>

<div class="wrap">

	<div id="icon-options-general" class="icon32"><br></div>

	<h2>Mock WP Plugin</h2>

	<?php if ($status['state'] == 'nominal'): ?>

		<?php if ( ! empty($errors)): ?>
			<br/>
			<p class="update-nag">
				<strong>Unable to save settings.</strong>
				Please check the fields for errors and typos.
			</p>
		<?php endif; ?>

		<?php echo $f = pixtypes::form($config, $processor) ?>

		<h3 style="display: none">General Settings</h3>

		<table class="form-table">

			<?php echo $f->field('article_settings_sample')
				->setmeta('note', 'These settings may be overridden for individual articles.')
				->render() ?>

		</table>

		<?php /* # sample block ?>

				<?php # HowTo: show all entries defined in the configuration ?>
				<?php echo $f->fieldtemplate
					(
						$coretemplatepath.'linear'.EXT,
						array('fields' => array_keys($config['fields']))
					) ?>

			<?php //*/# end sample block ?>

		<button type="submit" class="button button-primary">
			Save Changes
		</button>

		<?php echo $f->endform() ?>

	<?php elseif ($status['state'] == 'error'): ?>

		<h3>Critical Error</h3>

		<p><?php echo $status['message'] ?></p>

	<?php endif; ?>

</div>