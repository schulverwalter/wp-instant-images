<?php
/**
 * Image Sizes template.
 *
 * @package InstantImages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<h2 id="image-sizes"><?php esc_attr_e( 'Image Sizes', 'instant-images' ); ?></h2>
<p><?php esc_attr_e( 'The image sizes currently registered on this site. Uploaded images are generated in each of these sizes.', 'instant-images' ); ?></p>
<?php
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped in the method.
echo InstantImages::instant_images_display_image_sizes();
