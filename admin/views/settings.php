<?php
/**
 * Instant Images page settings.
 *
 * @package InstantImages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="wrap instant-images-settings">
	<?php require_once INSTANT_IMAGES_PATH . 'admin/includes/settings/header.php'; ?>

	<?php settings_errors(); ?>

	<?php
	// General Settings.
	require_once INSTANT_IMAGES_PATH . 'admin/includes/settings/general-settings.php';

	// Provider Settings.
	require_once INSTANT_IMAGES_PATH . 'admin/includes/settings/provider-settings.php';

	// API Settings.
	require_once INSTANT_IMAGES_PATH . 'admin/includes/settings/api-settings.php';

	// Image Sizes.
	require_once INSTANT_IMAGES_PATH . 'admin/includes/settings/image-sizes.php';
	?>
</div>
