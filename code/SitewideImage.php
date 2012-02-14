<?php
/**
 * Models images that should be universally available, regardless of subsite.
 */
class SitewideImage extends BetterImage {

	public function onBeforeWrite() {
		parent::onBeforeWrite();
		if( class_exists('Subsite') ) {
			$this->SubsiteID = 0;
		}
	}
}
