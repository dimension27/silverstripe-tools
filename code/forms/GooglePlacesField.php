<?php

class GooglePlacesField extends TextField {

	public function FieldHolder() {
		$this->rightTitle = '<div class="note">';
		if( $this->value ) {
			$this->rightTitle .= '<a href="http://maps.google.com.au/maps/place?cid='.$this->value
				.'">View place page</a>'.NL;
		}
		$this->rightTitle .= 'Copy and paste the full URL of the place page to update.</div>';
		return parent::FieldHolder();
	}

	/**
	 * Returns the field value suitable for insertion into the data object
	 */
	function dataValue() { 
		if( preg_match('/[?&]cid=([^?&]+)/', $this->value, $matches) ) {
			return $matches[1];
		}
	}

}

?>