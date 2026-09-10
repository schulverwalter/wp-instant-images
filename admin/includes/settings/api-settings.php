<?php
/**
 * API settings template.
 *
 * @package InstantImages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<form action="options.php" method="post" id="api-settings">
	<?php
	settings_fields( 'instant_images_api_settings_group' );
	do_settings_sections( 'instant-images-api' );
	?>
	<p class="description">
		<?php esc_attr_e( 'Leave a field empty to restore the default plugin key.', 'instant-images' ); ?>
	</p>
	<?php submit_button( __( 'Save API Keys', 'instant-images' ), 'primary', 'submit-api' ); ?>
</form>
