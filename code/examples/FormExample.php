<?php

class FormExample extends ContentController {

	function index() {
		return $this->render(array(
			'Form' => $this->DemoForm()
		));
	}

	function DemoForm() {
		$form = new DemoForm($this, 'DemoForm');
		$form->Fields()->push(new TextField('Outside', 'Outside'));
		return $form;
	}

}

class DemoForm extends Form {

	function __construct($controller, $name, FieldSet $actions, $validator = null) {
		$fields = new FieldSet();
		$fields->push(new TextField('Inside', 'Inside'));
		$actions = new FieldSet();
		$actions->push(new FormAction('doSubmit', 'Submit'));
		$actions->push(new FormAction('doFoobar', 'Foobar'));
		parent::__construct($controller, $name, $fields, $actions);
	}

	function doSubmit( $data ) {
		echo 'doSubmit called'.NL;
		/* debug */ Debug::show($data);
	}

	function doFoobar( $data ) {
		echo 'doFoobar called'.NL;
		/* debug */ Debug::show($data);
	}

}

?>