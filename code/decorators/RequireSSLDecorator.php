<?php
/**
 * Adds the ability to require an SSL connection for a page. Requires a call to 
 * RequireSSLDecorator::controller_init($this->data()) to be added to the Page_Controller.
 * @author simonwade
 */
class RequireSSLDecorator extends DataObjectDecorator {

	function extraStatics() {
		return array(
			'db' => array('RequireSSL' => 'Boolean')
		);
	}

	function updateCMSFields($fields) {
		$fields->addFieldToTab('Root.Behaviour', $field = new CheckboxField('RequireSSL', 'Require a secure SSL connection for this page?'), 'ProvideComments');
	}

	static function controller_init( $dataObject ) {
		if( $dataObject->RequireSSL && Director::isLive() ) {
			Director::forceSSL();
		}
	}

}

?>