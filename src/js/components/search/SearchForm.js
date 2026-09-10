import { forwardRef, useEffect, useRef, useState } from '@wordpress/element';
import classNames from 'classnames';
import { usePluginContext } from '../../common/pluginProvider';
import { getSearchHistory, saveSearchHistory } from '../../functions/localStorage';
import { useClickOutside } from '../../hooks/useClickOutside';
import SearchHistory from './SearchHistory';
import SearchToolTip from './SearchToolTip';

/**
 * Render the search form as a component.
 *
 * @return {JSX.Element} The SearchForm component.
 */
const SearchForm = forwardRef(({}, ref) => {
	const { searchHandler, apiError } = usePluginContext();
	const [history, setHistory] = useState([]);
	const [show, setShow] = useState(false);

	const historyRef = useRef(null);
	const submitBtnRef = useRef(null);

	// Handle clickoutside hook.
	useClickOutside(historyRef, () => {
		setShow(false);
	});

	/**
	 * Set the search value in the form.
	 *
	 * @param {string} value The value to set.
	 */
	function setSearchValue(value) {
		const input = ref?.current;
		input.value = value;
		submitBtnRef?.current.click();

		// Set focus on input.
		input.focus();
	}

	/**
	 * Search submit handler.
	 *
	 * @param {Event} e The event object.
	 */
	function formSubmit(e) {
		e.preventDefault();
		const term = ref?.current?.value;
		if (term) {
			searchHandler(e);
			saveSearchHistory(term);
			setHistory(getSearchHistory());
		}
	}

	useEffect(() => {
		setHistory(getSearchHistory());
	}, []);

	return (
		<div className={classNames('control-nav--search', apiError ? 'inactive' : null)}>
			<form onSubmit={(e) => formSubmit(e)} autoComplete="off">
				<label htmlFor="search-input" className="offscreen">
					{instant_img_localize.search_label}
				</label>
				<div ref={historyRef}>
					<input ref={ref} type="search" id="search-input" placeholder={instant_img_localize.search} disabled={apiError} onFocus={() => setShow(true)} />
					{!!history.length && <SearchHistory show={show} history={history} setHistory={setHistory} setSearchValue={setSearchValue} container={historyRef} />}
				</div>
				<button type="submit" className="button" disabled={apiError} ref={submitBtnRef}>
					{instant_img_localize.search_label}
				</button>
				<SearchToolTip show={show} />
			</form>
		</div>
	);
});
export default SearchForm;
