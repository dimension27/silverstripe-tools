<?php

/**
 * Email that gets sent to the people listed in the Email Recipients
 * when a submission is made
 * @package ss-tools.userforms
 */
class StyledSubmittedFormEmail extends SSTools_StyledEmail {

	protected $ss_template = 'SubmittedFormEmail';
	protected $values = array();

	function __construct() {
		$this->CSS = file_get_contents(Utils::ProjectDir() . '/templates/email/SubmittedFormEmail.css', FILE_USE_INCLUDE_PATH);
	}

	function IsUserDefinedForm() {
		return true;
	}

	public function populateTemplate($data) {
		parent::populateTemplate($data);
		if( isset($data->Subject) ) {
			$this->template_data['GroupEmailHeading'] = $data->Subject;
		}
		if( !is_object($data) && isset($data['Fields']) ) {
			foreach($data['Fields'] as $Field) {
				$this->values[$Field->Name] = $Field->Value;
			}
		}
	}

	public function setSubject($subject) {
		parent::setSubject($this->handleReplacements($subject));
	}

	public function setBody($body) {
		parent::setBody($this->handleReplacements($body));
	}

	public function handleReplacements($string) {
		if( isset($_REQUEST['PageURL']) ) {
			$this->values['PageURL'] = $_REQUEST['PageURL'];
		}
		foreach( $this->values as $name => $value ) {
			if( preg_match('!^https?://!', $value) ) {
				$value = "<a href='$value'>$value</a>";
			}
			else {
				$value = Convert::raw2xml($value);
			}
			$string = str_replace('$'.$name, $value, $string);
		}
		return $string;
	}

}

?>