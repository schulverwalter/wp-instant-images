import { useRef } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import classNames from 'classnames';
import { clearSearchHistory } from '../../functions/localStorage';
import { useArrowControls } from '../../hooks/useArrowControls';

/**
 * The History list component.
 *
 * @param {Object} props The component props.
 * @return {JSX.Element} The SearchHistory component.
 */
export default function SearchHistory(props) {
	const { show, history, setHistory, setSearchValue, container } = props;
	const dropRef = useRef(null);

	// Use up/down arrow keys to navigate dropdown.
	useArrowControls(show, container);

	return (
		<div className={classNames('control-nav--search-history', show ? 'active' : null)} role="listbox" ref={dropRef}>
			<div className="control-nav--search-history-title">
				<div>{__('Recent Searches', 'instant-images')}</div>
				<button
					type="button"
					onClick={() => {
						clearSearchHistory();
						setHistory([]);
					}}
				>
					{__('Clear', 'instant-images')}
				</button>
			</div>
			<ul role="listbox" className="search-history">
				{history.map((item, key) => (
					<li key={key} role="option">
						<button type="button" className="history" onClick={() => setSearchValue(item)}>
							{item}
						</button>
					</li>
				))}
			</ul>
		</div>
	);
}
