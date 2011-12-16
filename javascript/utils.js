jQuery.ssTools = {
	/**
	 * Workaround for the fact that SS replaces all hash links to work around its base tag.
	 * @param selector Optional selector for selecting links. Defaults to "a.hash-only, .hash-only a"
	 */
	fixHashLinks: function( selector ) {
		$(selector || 'a.hash-only, .hash-only a').each(function(key, value) {
			// handle the href with or without a leading slash at the start
			var href = $(this).attr('href').replace(/^\//, ''),
				location = window.location.pathname.replace(/^\//, '');
			href = href.replace(location, '').replace(window.location.search, '');
			$(this).attr('href', href);
		});
	}
};