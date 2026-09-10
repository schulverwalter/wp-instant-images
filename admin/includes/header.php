<?php
/**
 * Plugin header.
 *
 * @package InstantImages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<h1 class="wp-heading-inline"><?php echo esc_html( INSTANT_IMAGES_TITLE ); ?></h1>
<?php if ( $show_settings && InstantImages::instant_img_has_settings_access() ) : ?>
	<a href="<?php echo esc_url( INSTANT_IMAGES_WPADMIN_SETTINGS_URL ); ?>" class="page-title-action">
		<?php esc_attr_e( 'Settings', 'instant-images' ); ?>
	</a>
<?php endif; ?>
<hr class="wp-header-end">
<p class="description">
	<?php echo wp_kses_post( InstantImages::instant_images_get_tagline() ); ?>
</p>
