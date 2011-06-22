<?php

/**
 * Page type for creating a page that contains a form that visitors can use to manage their profile.
 * Based on implementation from SubscribeForm in the newsletter module.
 */
class MyProfilePage extends UserDefinedForm {

	static $db = array(
		'FieldMapping' => 'Text'
	);

	/**
	 * Password is automatically added.
	 * @var array
	 */
	static $member_field_map = array(
		'FirstName' => array('First name', 'EditableTextField(Required=1,CanDelete=0)'),
		'Surname' => array('Last name', 'EditableTextField(Required=1,CanDelete=0)'),
		'Email' => array('Email', 'EditableEmailField(Required=1,CanDelete=0)'),
	);

	static $defaults = array(
		'OnCompleteMessage' => "<p>Your details have been successfully updated.</p>",
	);

	function setFieldMap( $fields ) {
		self::$member_field_map = $fields;
	}

	protected function addDefaultFields() {
		$numFields = $this->Fields()->Count();
		$mapping = array();
		// check that the required fields exist
		foreach( self::$member_field_map as $memberField => $details ) {
			list($title, $typeString) = $details;
			list($type, $typeValue) = $this->parseType( $typeString );
			$field = new $type();
			$field->write();
			$field->Name = $field->class.$field->ID;
			$field->Title = $title;
			if( $typeValue )	{
				$field->prepopulate($typeValue);
			}
			$field->ParentID = $this->ID;
			$field->Sort = ++$numFields;
			$field->write();
			$mapping[$memberField] = $field->Name;
		}
		$this->FieldMapping = serialize($mapping);
		$this->write();
	}

	function getFieldMap() {
		static $mapping;
		if( !isset($mapping) ) {
			$mapping = unserialize($this->FieldMapping);
		}
		return $mapping;
	}

	function parseType( $typeString ) {
		if( preg_match('/^([A-Za-z_]+)\(([^)]+)\)$/', $typeString, $match ) ) {
			return array( $match[1], $match[2] );
		}
		else {
			return array( $typeString, null );
		}
	}

	public function write($showDebug = false, $forceInsert = false, $forceWrite = false) {
		$isNew = (!$this->ID);
		parent::write($showDebug, $forceInsert, $forceWrite);
		if( $isNew ) {
			$this->addDefaultFields();
		}
	}

}

class MyProfilePage_Controller extends UserDefinedForm_Controller {

	protected function getMember() {
		$this->Member = Member::currentUser(); /* @var $this->Member Member */
		if( !$this->Member ) {
			Security::permissionFailure();
			return;
		}
		return $this->Member;
	}

	public function Form() {
		// need to call Object::create('MyProfilePage') in case there are Decorators that call setFieldMap()
		$page = Object::create('MyProfilePage');
		$member = $this->getMember();
		
		$form = parent::Form();
		
		$form->Fields()->push(new HiddenField('ID', 'ID', @$member->ID));
		$form->Fields()->push($password = new ConfirmedPasswordField(
			'Password', null, null, null, true // showOnClick
		));
		$password->setForm($form);
		$password->setCanBeEmpty(true);
		if( !$member || !$member->ID ) {
			$password->showOnClick = false;
		}
		$data = array();
		if( $member && $member->ID ) {
			foreach( $this->owner->getFieldMap() as $memberField => $formField ) {
				$data[$formField] = $member->$memberField;
			}
			$form->loadDataFrom($data);
		}
		return $form;
	}

	public function process( $data, Form $form ) {
		$member = $this->getMember();
		foreach( $this->owner->getFieldMap() as $memberField => $formField ) {
			$data[$memberField] = $data[$formField];
		}
		// @todo The required fields need to be mapped too
		$mapping = $this->owner->getFieldMap();
		$mapping = array_combine(array_values($mapping), array_keys($mapping));
		$required = $form->getValidator()->getRequired();
		$memberForm = new Form($this, 'Member', $form->Fields(), new FieldSet(), new Member_Validator($required));
		foreach( $memberForm->Fields() as $field ) {
			if( isset($mapping[$field->Name()]) ) {
				$field->setName($mapping[$field->Name()]);
			}
		}
		$memberForm->loadDataFrom($data);
		try {
			$memberForm->saveInto($member);
			$member->write();
		}
		catch(ValidationException $e) {
			$form->sessionMessage($e->getResult()->message(), 'bad');
			return Director::redirectBack();
		}
		return parent::process( $data, $form );
	}

	public function getMappedData() {
		$values = array();
		foreach( $this->owner->getFieldMap() as $memberField => $formField ) {
			$values[$memberField] = $this->request->requestVar($formField);
		}
		return $values;
	}

}
