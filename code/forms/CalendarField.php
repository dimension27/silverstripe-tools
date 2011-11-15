<?php

class CalendarField extends DateField {

	function __construct( $name, $title = null, $value = null, $form = null, $rightTitle = null ) {
		parent::__construct($name, $title, $value, $form, $rightTitle);
		$this->setConfig('showcalendar', true);
	}

}

?>