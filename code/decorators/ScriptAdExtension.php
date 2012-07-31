<?php

class ScriptAdExtension extends DataObjectDecorator {

	function extraStatics() {
		return array(
			'db' => array(
				'Script' => 'Text'
			)
		);
	}

	function updateCMSFields( FieldSet $fields ) {
		$fields->addFieldToTab('Root.Main', $field = new TextareaField('Script'), 'InternalPageID');
	}

}

?>