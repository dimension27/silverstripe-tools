<?php
class FilesystemPublisherExtension extends SiteTreeDecorator {

	public static $update_siblings_on_publish = true;
	public static $cache_filter = "ClassName != 'UserDefinedForm'";

	/**
	 * Return a list of all the pages to cache
	 */
	function allPagesToCache() {
		// Get each page type to define its sub-urls
		$urls = array();
		if( class_exists('Subsite') ) {
			$pages = Subsite::get_from_all_subsites('SiteTree', self::$cache_filter);
		}
		else {
			$pages = DataObject::get('SiteTree', self::$cache_filter);
		}
		foreach( $pages as $page ) {
			$urls = array_merge($urls, $page->getURLsToCache());
		}
		// add any custom URLs which are not SiteTree instances
		$urls[] = Director::absoluteBaseURL().'sitemap.xml';
		return $urls;
	}

	function getURLsToCache() {
		// Defines any pages which should not be cached
		$excluded = array();
		$urls = array();
		if( $this->owner->canView() ) {
			$urls[] = $this->owner->AbsoluteLink();
		}
		$urls = array_merge($urls, $this->owner->subPagesToCache());
		$rv = array();
		foreach( $urls as $url ) {
			if( !in_array($url, $excluded) ) {
				$rv[] = $url;
			}
		}
		if( self::$update_siblings_on_publish ) {
			if( $p = $this->owner->Parent ) {
				$siblings = $p->Children();
			}
			else {
				$siblings = DataObject::get('SiteTree', 'ParentID = 0 && '.self::$cache_filter);
			}
			foreach( $siblings as $sibling ) {
				$urls[] = $sibling->AbsoluteLink();
				$urls = array_merge($urls, (array) $sibling->subPagesToCache());
			}
		}
		//* debug */ Debug::show($urls);
		return $urls;
	}

	/**
	 * Get a list of URLs to cache related to this page
	 */
	function subPagesToCache() {
		$urls = array();
		// only cache the RSS feed if anyone can view this page
		if( $this->owner->ProvideComments && $this->owner->canView() ) {
			$urls[] = Director::absoluteBaseURL().'pagecomment/rss/'.$this->ID;
		}
		return $urls;
	}

}

?>