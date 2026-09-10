<?php
/**
 * Instant Images Settings.
 *
 * @package InstantImages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Initiate the plugin setting, create settings variables.
 *
 * @author ConnektMedia <support@connekthq.com>
 * @since 2.0
 */
function instant_images_admin_init() {
	$providers = InstantImages::instant_img_get_providers();

	// General Settings.
	register_setting(
		'instant_images_general_settings_group',
		INSTANT_IMAGES_SETTINGS,
		'instant_images_sanitize'
	);

	add_settings_section(
		'instant_images_general_settings',
		__( 'General Settings', 'instant-images' ),
		'instant_images_general_settings_callback',
		'instant-images'
	);

	// Download Width.
	add_settings_field(
		'unsplash_download_w',
		__( 'Upload Image Width', 'instant-images' ),
		'instant_images_width_callback',
		'instant-images',
		'instant_images_general_settings'
	);

	// Download Height.
	add_settings_field(
		'unsplash_download_h',
		__( 'Upload Image Height', 'instant-images' ),
		'instant_images_height_callback',
		'instant-images',
		'instant_images_general_settings'
	);

	// Auto captions.
	add_settings_field(
		'auto_attribution',
		__( 'Auto Captions/Attribution', 'instant-images' ),
		'instant_images_auto_attribution_callback',
		'instant-images',
		'instant_images_general_settings'
	);

	// Media Modal.
	add_settings_field(
		'media_modal_display',
		__( 'Media Tab', 'instant-images' ),
		'instant_images_media_modal_display_callback',
		'instant-images',
		'instant_images_general_settings'
	);

	// Provider Config (order and active state).
	register_setting(
		'instant_images_provider_settings_group',
		INSTANT_IMAGES_PROVIDER_SETTINGS,
		'instant_images_sanitize_providers'
	);

	// API Keys.
	register_setting(
		'instant_images_api_settings_group',
		INSTANT_IMAGES_API_SETTINGS,
		'instant_images_sanitize'
	);

	add_settings_section(
		'instant_images_api_settings',
		__( 'API Keys', 'instant-images' ),
		'instant_images_api_settings_callback',
		'instant-images-api'
	);

	// Upgrade routine.
	$general_options  = get_option( INSTANT_IMAGES_SETTINGS ); // General options.
	$settings_updated = get_option( 'instant_img_settings_updated' );

	// Providers API Keys.
	$upgrade_routine = [];
	$count           = 0;
	foreach ( $providers as $provider ) {
		if ( $provider['requires_key'] ) {
			++$count;
			$key   = $provider['slug'] . '_api';
			$title = $provider['name'] . __( 'API Key', 'instant-images' );

			// Upgrade routine.
			if ( ! $settings_updated && isset( $general_options [ $key ] ) && $general_options [ $key ] ) {
				$upgrade_routine[ $key ] = $general_options [ $key ];
				update_option( INSTANT_IMAGES_API_SETTINGS, $upgrade_routine );
				unset( $general_options [ $key ] );
				update_option( INSTANT_IMAGES_SETTINGS, $general_options );
				update_option( 'instant_img_settings_updated', true );
			}

			add_settings_field(
				$key,
				$title,
				'instant_images_api_keys_callback',
				'instant-images-api',
				'instant_images_api_settings',
				[ $provider ]
			);
		}
	}
}
add_action( 'admin_init', 'instant_images_admin_init' );

/**
 * General settings text.
 *
 * @author ConnektMedia <support@connekthq.com>
 * @since 1.0
 */
function instant_images_general_settings_callback() {
	echo '<p>' . esc_attr__( 'Manage your media upload settings.', 'instant-images' ) . '</p>';
}

/**
 * API Key settings text.
 *
 * @author ConnektMedia <support@connekthq.com>
 * @since 5.3
 */
function instant_images_api_settings_callback() {
	echo '<p>' . esc_attr__( 'Manage your provider API keys.', 'instant-images' ) . '</p>';
}

/**
 * Sanitize form fields.
 *
 * @author ConnektMedia <support@connekthq.com>
 * @since 1.0
 * @param array $input Array of field values.
 * @return array $output Sanitized field values.
 */
function instant_images_sanitize( $input ) {

	// Create our array for storing the validated options.
	$output = [];
	// Loop through each of the incoming options.
	foreach ( $input as $key => $value ) {
		// Check to see if the current option has a value. If so, process it.
		if ( isset( $input[ $key ] ) ) {
			// Strip all HTML and PHP tags and properly handle quoted strings.
			$output[ $key ] = wp_strip_all_tags( stripslashes( $input[ $key ] ) );
		}
	}

	// Return the array processing any additional functions filtered by this action.
	return apply_filters( 'instant_images_validate_input_settings', $output, $input );
}

/**
 * Max File download width.
 *
 * @author ConnektMedia <support@connekthq.com>
 * @since 1.0
 */
function instant_images_width_callback() {
	$options = get_option( INSTANT_IMAGES_SETTINGS );
	$options = is_array( $options ) ? $options : [];

	if ( ! isset( $options['unsplash_download_w'] ) ) {
		$options['unsplash_download_w'] = '1600';
	}

	echo '<input type="number" id="instant_img_settings[unsplash_download_w]" name="instant_img_settings[unsplash_download_w]" value="' . esc_attr( $options['unsplash_download_w'] ) . '" class="small-text" step="20" max="4800" /> ';
	echo '<p class="description">' . esc_attr__( 'Maximum width, in pixels, of uploaded images.', 'instant-images' ) . '</p>';
}

/**
 * Max File download height.
 *
 * @author ConnektMedia <support@connekthq.com>
 * @since 1.0
 */
function instant_images_height_callback() {
	$options = get_option( INSTANT_IMAGES_SETTINGS );
	$options = is_array( $options ) ? $options : [];
	if ( ! isset( $options['unsplash_download_h'] ) ) {
		$options['unsplash_download_h'] = '1200';
	}

	echo '<input type="number" id="instant_img_settings[unsplash_download_h]" name="instant_img_settings[unsplash_download_h]" value="' . esc_attr( $options['unsplash_download_h'] ) . '" class="small-text" step="20" max="4800" /> ';
	echo '<p class="description">' . esc_attr__( 'Maximum height, in pixels, of uploaded images.', 'instant-images' ) . '</p>';
}

/**
 * Automatically add image attribution to captions.
 *
 * @author ConnektMedia <support@connekthq.com>
 * @since 5.2.0
 */
function instant_images_auto_attribution_callback() {
	$label = __( 'Automatically add image attribution (as captions) when uploading images.', 'instant-images' );

	echo instant_images_settings_checkbox( 'auto_attribution', $label ); // phpcs:ignore
}

/**
 * Show the Instant Images Tab in Media Modal.
 *
 * @author ConnektMedia <support@connekthq.com>
 * @since 3.2.1
 */
function instant_images_media_modal_display_callback() {
	$label = __( 'Remove the Instant Images tab in the Media Modal.', 'instant-images' );

	echo instant_images_settings_checkbox( 'media_modal_display', $label ); // phpcs:ignore
}

/**
 * Render a standard WordPress checkbox setting.
 *
 * @param string $name    The option name.
 * @param string $label   The checkbox label.
 * @param string $default The default setting.
 * @return string The HTML for the checkbox.
 */
function instant_images_settings_checkbox( $name, $label, $default = '0' ) {
	$options = get_option( INSTANT_IMAGES_SETTINGS );
	$options = is_array( $options ) ? $options : [];

	if ( ! isset( $options[ $name ] ) ) {
		$options[ $name ] = $default;
	}

	$html  = '<label for="' . esc_attr( $name ) . '">';
	$html .= '<input type="checkbox" name="instant_img_settings[' . esc_attr( $name ) . ']" id="' . esc_attr( $name ) . '" value="1"' . ( $options[ $name ] ? ' checked="checked"' : '' ) . ' /> ';
	$html .= esc_html( $label );
	$html .= '</label>';

	return $html;
}

/**
 * Set the API keys for each required provider.
 *
 * @param array $args Provider arguments.
 * @author ConnektMedia <support@connekthq.com>
 * @since 5.3
 */
function instant_images_api_keys_callback( $args = [] ) {
	$provider = isset( $args[0] ) && isset( $args[0] ) ? $args[0] : false;
	if ( ! $provider ) {
		// Bail early if no provider is set.
		return;
	}

	$options  = get_option( INSTANT_IMAGES_API_SETTINGS ); // API options.
	$options  = is_array( $options ) ? $options : []; // API options.
	$key      = $provider['slug'] . '_api';
	$title    = $provider['name'];
	$constant = $provider['constant'];
	$url      = $provider['url'];
	$readonly = '';
	$disabled = '';

	if ( defined( $constant ) ) {
		$readonly        = ' readonly';
		$disabled        = ' disabled';
		$options[ $key ] = constant( $constant );
	} elseif ( ! isset( $options[ $key ] ) ) {
			$options[ $key ] = '';
	}

	printf(
		'<input type="text" id="%1$s" name="%1$s" value="%2$s" class="regular-text"%3$s />',
		esc_attr( 'instant_img_api_settings[' . $key . ']' ),
		esc_attr( $options[ $key ] ),
		esc_attr( $readonly . $disabled )
	);

	echo '<p class="description">';
	if ( defined( $constant ) ) {
		echo esc_html__( 'API key has been set via site constant.', 'instant-images' );
	} else {
		printf(
			'<a href="%1$s" target="_blank">%2$s</a>',
			esc_url( $url ),
			/* translators: %s: Provider name. */
			esc_html( sprintf( __( 'Get a free %s API key', 'instant-images' ), ucfirst( $title ) ) )
		);
	}
	echo '</p>';
}

/**
 * Sanitize the provider config option.
 *
 * @param mixed $input The submitted value (JSON string of active provider slugs).
 * @return array Sanitized array of provider slugs.
 */
function instant_images_sanitize_providers( $input ) {
	if ( is_string( $input ) ) {
		$input = json_decode( stripslashes( $input ), true );
	}
	if ( ! is_array( $input ) || empty( $input ) ) {
		// Return all providers if invalid.
		$providers = InstantImages::instant_img_get_providers();
		return array_map(
			function ( $p ) {
				return $p['slug'];
			},
			$providers
		);
	}
	$valid_slugs = array_map(
		function ( $p ) {
			return $p['slug'];
		},
		InstantImages::instant_img_get_providers()
	);
	return array_values(
		array_filter(
			array_map( 'sanitize_text_field', $input ),
			function ( $slug ) use ( $valid_slugs ) {
				return in_array( $slug, $valid_slugs, true );
			}
		)
	);
}

/**
 * Empty callback function for the API Key switcher.
 *
 * @author ConnektMedia <support@connekthq.com>
 * @since 4.6
 */
function instant_images_callable() {
	return null;
}
