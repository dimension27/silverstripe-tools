<?php

class RequireSSLDecorator extends DataObjectDecorator {

	function extraStatics() {
		return array(
			'db' => array('RequireSSL' => 'Boolean')
		);
	}

	function updateCMSFields($fields) {
		/* debug */ Debug::show('foo');
		$fields->addFieldToTab('Root.Behaviour', $field = new CheckboxField('RequireSSL', 'Require a secure SSL connection for this page?'), 'ProvideComments');
	}

	static function controller_init( $dataObject ) {
		if( $dataObject->RequireSSL && Director::isLive() ) {
			Director::forceSSL();
		}
	}

}

?>