<?php

/**
 * Example of how to customise the login form.
 * Usage:
 * Object::useCustomClass('MemberLoginForm', 'MyLoginForm');
 * The markup around the form can be customised by overriding Security_login.ss.
 *
class MyLoginForm extends MemberLoginForm {

	function __construct( $controller, $name, $fields = null, $actions = null, $checkCurrentUser = true ) {
		parent::__construct($controller, $name, $fields, $actions, $checkCurrentUser);
		if( $field = $this->actions->fieldByName('action_dologin') ) {
			$field->setTitle('Submit Login Details');
		}
	}

}
 */

?>