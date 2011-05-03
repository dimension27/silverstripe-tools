<?php

class MyProfilePage extends Page {
}

class MyProfilePage_Controller extends Page_Controller {

	public function init() {
		Requirements::javascript(SAPPHIRE_DIR . "/thirdparty/prototype/prototype.js");
		Requirements::javascript(SAPPHIRE_DIR . "/thirdparty/behaviour/behaviour.js");
		Requirements::javascript(SAPPHIRE_DIR . "/javascript/prototype_improvements.js");
		Requirements::javascript(THIRDPARTY_DIR . "/scriptaculous/scriptaculous.js");
		Requirements::javascript(THIRDPARTY_DIR . "/scriptaculous/controls.js");
		Requirements::javascript(SAPPHIRE_DIR . "/javascript/layout_helpers.js");
		Requirements::css(SAPPHIRE_DIR . "/css/MemberProfileForm.css");
		parent::init();
	}

	public function Form() {
		$this->Member = Member::currentUser(); /* @var $this->Member Member */
		if( !$this->Member ) {
			Security::permissionFailure();
			return;
		}
		$form = new MyProfilePage_Form($this, 'Form', $this->Member);
		$this->extend('updateForm', $form);
		return $form;
	}

}

/**
 * Form for editing a member profile. Solution copied from Member_ProfileForm.
 */
class MyProfilePage_Form extends Form {
	
	function __construct($controller, $name, $member) {
		$fields = $member->getCMSFields();
		$fields->push(new HiddenField('ID','ID',$member->ID));
		$actions = new FieldSet(
			new FormAction('dosave',_t('CMSMain.SAVE', 'Save'))
		);
		$validator = new Member_Validator();
		parent::__construct($controller, $name, $fields, $actions, $validator);
		$this->loadDataFrom($member);
	}
	
	function dosave($data, $form) {
		// don't allow ommitting or changing the ID
		if(!isset($data['ID']) || $data['ID'] != Member::currentUserID()) {
			return Director::redirectBack();
		}
		
		$SQL_data = Convert::raw2sql($data);
		$member = DataObject::get_by_id("Member", $SQL_data['ID']);
		
		if($SQL_data['Locale'] != $member->Locale) {
			$form->addErrorMessage("Generic", _t('Member.REFRESHLANG'),"good");
		}
		
		$form->saveInto($member);
		$member->write();
		
		$message = _t('Member.PROFILESAVESUCCESS', 'Successfully saved.');
		$form->sessionMessage($message, 'good');
		
		Director::redirectBack();
	}

}

class MyProfilePageDecorator extends Extension {

	public function updateForm( Form $form ) {
		$fields = $form->Fields();
		// remove all fields except what we want to keep
		$fieldsToKeep = array('FirstName', 'Surname', 'Telephone', 'Email', 'Password');
		foreach( $fieldsToKeep as $name ) {
			// push them onto the FieldSet because we don't want tabs
			$fields->push($fields->fieldByName('Root.Main.'.$name));
		}
		// IE6 has an issue with ConfirmedPasswordField showing on click
		$fields->replaceField('Password', $field = new ConfirmedPasswordField('Password', null, null, $form, false));
		$field->setCanBeEmpty(true);
		
		// remove the Main tab, but keep the Root tab because otherwise the change password doesn't work
		$fields->removeByName('Main');
		$fields->removeByName('Permissions');
		$fields->removeByName('Groups');
	}

}
