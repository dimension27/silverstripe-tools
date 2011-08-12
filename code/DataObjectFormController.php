<?php

abstract class DataObjectFormController extends Page_Controller {

	public $dataObject;
	public $dataObjectClass;
	public $formClass = 'DataObjectForm';
	public $formName = 'Form';
	public $htmlID;
	
	public function index( SS_HTTPRequest $request ) {
		$name = $this->formName;
		return $this->render(array(
			'Form' => $this->$name()
		));
	}

	/**
	 * Returns the DataObject to be edited.
	 * @return DataObject
	 */
	public function getDataObject() {
		return DataObject::get_by_id($this->dataObjectClass, $this->request->param('ID'));
	}

	/**
	 * Returns the Form for editing the DataObject.
	 * @return Form
	 */
	public function Form() {
		$dataObject = $this->getDataObject();
		$form = new $this->formClass($this, $this->formName, $dataObject);
		if( $message = $this->dataRecord->OnComplete ) {
			$form->setSuccessMessage($message);
		}
		$form->setHTMLID(get_class($dataObject).'_'.$this->formName);
		return $form;
	}

	public function complete( SS_HTTPRequest $request ) {
		return $this->render(array(
			'Content' => $this->dataRecord->OnComplete,
			'Form' => ''
		));
	}
	
}

class DataObjectForm extends Form {

	/**
	 * @var DataObject
	 */
	public $dataObject;

	public $successMessage;

	function __construct( $controller, $name, $dataObject ) {
		$this->dataObject = $dataObject;
		$fields = $dataObject->getCMSFields();
		$fields->push(new HiddenField('ID', 'ID', $dataObject->ID));
		$actions = new FieldSet(
			new FormAction('doSave', _t('CMSMain.SAVE', 'Save'))
		);
		$validator = null;
		parent::__construct($controller, $name, $fields, $actions, $validator);
		$this->loadDataFrom($dataObject);
	}

	function doSave( $data, Form $form ) {
		// don't allow ommitting or changing the ID
		if( @$data['ID'] != $this->dataObject->ID ) {
			return Director::redirectBack();
		}
		$form->saveInto($this->dataObject);
		$this->dataObject->write();
		$form->sessionMessage($this->getSuccessMessage(), 'good');
		// @todo Redirect to /complete to display the OnComplete content (see UserDefinedForm example)
		Director::redirect('complete');
	}

	function getSuccessMessage() {
		if( $this->successMessage ) {
			return $this->successMessage;
		}
		return 'The '.$this->dataObject->i18n_singular_name().' has been successfully updated.';
	}

	function setSuccessMessage( $message ) {
		$this->successMessage = $message;
	}

}

class OnCompleteDecorator extends DataObjectDecorator {

	function extraStatics() {
		return array(
			'db' => array(
				'OnComplete' => 'HTMLText'
			)
		);
	}

	function updateCMSFields( $fields ) {
		$fields->addFieldToTab('Root.Content.OnComplete', $field = new HtmlEditorField('OnComplete', 'On Complete'));
	}

}