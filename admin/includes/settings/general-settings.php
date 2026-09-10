<?php
/**
 * General settings template.
 *
 * @package InstantImages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<form action="options.php" method="post" id="general-settings">
	<?php
	settings_fields( 'instant_images_general_settings_group' );
	do_settings_sections( 'instant-images' );
	submit_button( __( 'Save Settings', 'instant-images' ), 'primary', 'submit-general' );
	?>
</form>
