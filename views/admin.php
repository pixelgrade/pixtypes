<?php
	/**
	 * Represents the view for the administration dashboard.
	 *
	 * This includes the header, options, and other information that should
	 * provide the user interface to the end user.
	 *
	 * @package   PixTypes
	 * @author    Pixelgrade <contact@pixelgrade.com>
	 * @license   GPL-2.0+
	 * @link      http://pixelgrade.com
	 * @copyright 2013 Pixel Grade Media
	 */

	$config = include pixtypes::pluginpath().'plugin-config'.EXT;

	// invoke processor
	$processor = pixtypes::processor($config);
	$status = $processor->status();
	$errors = $processor->errors();
?>


<div class="wrap" id="pixtypes_form">

	<div id="icon-options-general" class="icon32"><br></div>

	<h2>Pixtypes</h2>

	<?php if ($processor->ok()): ?>

		<?php if ( ! empty($errors)): ?>
			<br/>
			<p class="update-nag">
				<strong>Unable to save settings.</strong>
				Please check the fields for errors and typos.
			</p>
		<?php endif; ?>

		<?php if ($processor->performed_update()): ?>
			<br/>
			<p class="update-nag">
				Settings have been updated.
			</p>
		<?php endif; ?>

		<?php echo $f = pixtypes::form($config, $processor) ?>

			<h3>Post Types</h3>

			<?php echo $f->field('post_types')->render() ?>

			<h3>Taxonomies</h3>

			<?php echo $f->field('taxonomies')->render() ?>

			<button type="submit" class="button button-primary">
				Save Changes
			</button>

		<?php echo $f->endform() ?>

	<?php elseif ($status['state'] == 'error'): ?>

		<h3>Critical Error</h3>

		<p><?php echo $status['message'] ?></p>

	<?php endif; ?>

	<?php  $options = get_option('pixtypes_settings');
	if ( isset( $options['themes'] ) && count($options['themes']) > 1 ) { ?>

		<div class="uninstall_area">
			<h3> Danger Zone </h3>

			<form method="post" id="unset_pixypes" action="<?php echo admin_url('options-general.php?page=pixtypes') ?>" >
				<input type="hidden" name="unset_nonce" value="<?php echo wp_create_nonce('unset_pixtype') ?>" />
				<ul>
					<?php
					if ( isset( $_POST['unset_pixtype'] ) && isset( $_POST['unset_nonce'] ) && wp_verify_nonce( $_POST['unset_nonce'], 'unset_pixtype' ) ) {
						$key = $_POST['unset_pixtype'];
						if ( isset( $options['themes'][$key] )) {
							unset($options['themes'][$key]);
							update_option('pixtypes_settings', $options);
						}
					}

					if ( isset( $options['themes'] ) && count( $options['themes'] ) > 1 ) {
						foreach( $options['themes'] as $key => $theme ){
							echo '<li><button class="button delete-action" type="submit" name="unset_pixtype" value="'. $key .'">Unset '.$key.'</button></li>';
						}
					} ?>
				</ul>
			</form>
		</div>
	<?php } ?>
</div>