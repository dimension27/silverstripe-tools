<?php

class RegisterPage extends MyProfilePage {

	static $defaults = array(
		'OnCompleteMessage' => "<p>Thanks for registering.</p>",
	);

}

class RegisterPage_Controller extends MyProfilePage_Controller {

	protected function getMember() {
		if( !isset($this->Member) ) {
			$this->Member = DataObject::create('Member');
		}
		return $this->Member;
	}

}

class RegisterEmailExtension extends Extension {

	function onBeforeSend( $email, $emailData, $emailRecipient ) {
		$data = $this->owner->getMappedData();
		$data['Password'] = $this->owner->request->requestVar('Password');
		$data['Password'] = @$data['Password']['_Password'];
		$replace = array();
		foreach( $data as $name => $value ) {
			$replace['{$'.$name.'}'] = $value;
		}
		$body = str_ireplace(array_keys($replace), array_values($replace), $email->Body());
		return $body;
	}

}

?>