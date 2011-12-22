<?php

/**
 * Adds a GooglePlus button to a template.
 * See http://www.google.com/webmasters/+1/button/
 * @author simonwade
 */

class SSTools_Social_GooglePlusButton {

	public $size = 'medium';
	public $annotation = 'none';

	function forTemplate() {
		Requirements::javascript('https://apis.google.com/js/plusone.js');
		return "<g:plusone size='$this->size' annotation='$this->annotation'></g:plusone>";
	}

}
