<?php
/**
 * Provider settings template.
 *
 * @package InstantImages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$all_providers    = InstantImages::instant_img_get_providers();
$active_providers = InstantImages::instant_img_get_active_providers();
$all_slugs        = array_map(
	function ( $p ) {
		return $p['slug'];
	},
	$all_providers
);

// Build ordered list: active providers first (in order), then inactive ones.
$ordered_providers = [];
foreach ( $active_providers as $slug ) {
	foreach ( $all_providers as $provider ) {
		if ( $provider['slug'] === $slug ) {
			$provider['active']  = true;
			$ordered_providers[] = $provider;
			break;
		}
	}
}
foreach ( $all_providers as $provider ) {
	if ( ! in_array( $provider['slug'], $active_providers, true ) ) {
		$provider['active']  = false;
		$ordered_providers[] = $provider;
	}
}
?>
<h2 id="provider-settings"><?php esc_attr_e( 'Providers', 'instant-images' ); ?></h2>
<p><?php esc_attr_e( 'Enable, disable, and reorder your image providers. Drag an entry to change the order of the provider tabs.', 'instant-images' ); ?></p>
<form action="options.php" method="post" id="provider-settings-form">
	<?php settings_fields( 'instant_images_provider_settings_group' ); ?>
	<input type="hidden" id="instant_img_provider_config" name="instant_img_provider_config" value="" />
	<ul id="provider-sortable-list" class="provider-sortable-list">
		<?php foreach ( $ordered_providers as $provider ) : ?>
			<li
				class="provider-sortable-item<?php echo $provider['active'] ? ' active' : ' inactive'; ?>"
				data-slug="<?php echo esc_attr( $provider['slug'] ); ?>"
				draggable="true"
			>
				<span class="provider-sortable-item--handle" aria-hidden="true" title="<?php esc_attr_e( 'Drag to reorder', 'instant-images' ); ?>">
					<span class="dashicons dashicons-menu"></span>
				</span>
				<label>
					<input
						type="checkbox"
						<?php checked( $provider['active'] ); ?>
						data-slug="<?php echo esc_attr( $provider['slug'] ); ?>"
					/>
					<?php echo esc_html( $provider['name'] ); ?>
				</label>
			</li>
		<?php endforeach; ?>
	</ul>
	<p class="submit">
		<?php submit_button( __( 'Save Providers', 'instant-images' ), 'primary', 'submit-providers', false ); ?>
		<button type="button" class="button" id="provider-reset-btn">
			<?php esc_attr_e( 'Reset', 'instant-images' ); ?>
		</button>
	</p>
</form>
<script>
(function() {
	const list = document.getElementById('provider-sortable-list');
	const hiddenInput = document.getElementById('instant_img_provider_config');
	const defaultOrder = <?php echo wp_json_encode( $all_slugs ); ?>;
	let dragItem = null;

	function updateHiddenInput() {
		const items = list.querySelectorAll('.provider-sortable-item');
		const active = [];
		items.forEach(function(item) {
			const checkbox = item.querySelector('input[type="checkbox"]');
			if (checkbox && checkbox.checked) {
				active.push(item.getAttribute('data-slug'));
			}
		});

		// Prevent deactivating all providers.
		if (active.length === 0) {
			const firstCheckbox = list.querySelector('.provider-sortable-item input[type="checkbox"]');
			if (firstCheckbox) {
				firstCheckbox.checked = true;
				const firstItem = firstCheckbox.closest('.provider-sortable-item');
				firstItem.classList.remove('inactive');
				firstItem.classList.add('active');
				active.push(firstItem.getAttribute('data-slug'));
			}
		}

		hiddenInput.value = JSON.stringify(active);
	}

	// Toggle handler.
	list.addEventListener('change', function(e) {
		if (e.target.type === 'checkbox') {
			const item = e.target.closest('.provider-sortable-item');
			if (e.target.checked) {
				item.classList.remove('inactive');
				item.classList.add('active');
			} else {
				item.classList.remove('active');
				item.classList.add('inactive');
			}
			updateHiddenInput();
		}
	});

	// Drag and drop.
	list.addEventListener('dragstart', function(e) {
		dragItem = e.target.closest('.provider-sortable-item');
		if (dragItem) {
			dragItem.classList.add('dragging');
			e.dataTransfer.effectAllowed = 'move';
		}
	});

	list.addEventListener('dragend', function() {
		if (dragItem) {
			dragItem.classList.remove('dragging');
			dragItem = null;
			updateHiddenInput();
		}
	});

	list.addEventListener('dragover', function(e) {
		e.preventDefault();
		e.dataTransfer.dropEffect = 'move';
		const target = e.target.closest('.provider-sortable-item');
		if (target && target !== dragItem) {
			const rect = target.getBoundingClientRect();
			const midY = rect.top + rect.height / 2;
			if (e.clientY < midY) {
				list.insertBefore(dragItem, target);
			} else {
				list.insertBefore(dragItem, target.nextSibling);
			}
		}
	});

	// Reset button handler.
	document.getElementById('provider-reset-btn').addEventListener('click', function() {
		// Reorder items to default order and enable all.
		defaultOrder.forEach(function(slug) {
			const item = list.querySelector('[data-slug="' + slug + '"]');
			if (item) {
				const checkbox = item.querySelector('input[type="checkbox"]');
				checkbox.checked = true;
				item.classList.remove('inactive');
				item.classList.add('active');
				list.appendChild(item);
			}
		});
		updateHiddenInput();

		// Submit the form once the UI has settled.
		setTimeout(function() {
			document.getElementById('submit-providers').click();
		}, 250);
	});

	// Initialize hidden input on page load.
	updateHiddenInput();
})();
</script>
