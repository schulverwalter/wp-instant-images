import { __, sprintf } from '@wordpress/i18n';
import { usePluginContext } from '../../common/pluginProvider';

/**
 * Render the SearchHeader component.
 *
 * @return {JSX.Element} The SearchHeader component.
 */
export default function SearchHeader() {
	const { search, getPhotos } = usePluginContext();
	const { active = false, term = '', results: total = 0 } = search;

	if (!active) {
		// Exit if search is not active.
		return null;
	}

	const label = term.replace('id:', 'ID: ');

	return (
		<header className="search-header">
			<h2>
				{sprintf(
					/* translators: %s: The search term. */
					__('Search results for: %s', 'instant-images'),
					label,
				)}
			</h2>
			<div className="search-header--text">
				<span>{`${total} ${instant_img_localize.search_results} ${label}`}</span>
				<button type="button" className="button-link" onClick={() => getPhotos(true)}>
					{instant_img_localize.clear_search}
				</button>
			</div>
		</header>
	);
}
