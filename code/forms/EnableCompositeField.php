<?php
/**
 * Allows a group of fields to be enabled/disabled based on the value of a boolean field.
 * @package SSTools_Forms
 */
class SSTools_Forms_EnableCompositeField extends CompositeField {
	
	/**
	 * Used to control the fields in the rest of the set.
	 */
	protected $controllerField = null;
	
	public function __construct($controllerField, $children = null) {
		if (!is_object($controllerField)) {
			$controllerField = new CheckboxField($controllerField);
		}
		$this->controllerField = $controllerField;
		parent::__construct($children);
		$this->addExtraClass('EnableCompositeField');
	}
	
	/**
	 * @param FieldSet $children
	 */
	public function setChildren($children) {
		parent::setChildren($children);
		$this->children->insertFirst($this->controllerField);
		$this->columnCount = $this->children->Count();
		if ($this->columnCount > 3) {
			$this->columnCount = null;
		}
	}
	
	protected function getFieldHolderContent($subfieldCall) {
		$disabled = !$this->controllerField->Value();
		if ($disabled) {
			$fieldSet = new FieldSet();
			$disable = new DisabledTransformation();
			foreach ($this->FieldSet() as $index => $field) {
				if ($index > 0) {
					$fieldSet->push($field->transform($disable));
				}
				
			}
			$this->setChildren($fieldSet);
		}
		Requirements::onDocumentReady($this->getControllerJs());
		return parent::getFieldHolderContent($subfieldCall);
		
	}
	
	protected function getControllerJs() {
		$controllerId = $this->controllerField->ID();
		return <<< EOD
$('#$controllerId').bind({
'change': function () {
	var that = $(this), field = that.closest('div.field'), disabled = !that.attr('checked');
	field.children().each(function(index, child){
		if (index > 0) {
			$(':input', child).attr('disabled', disabled);
		}
	});
}
}); 

EOD;
	}
}

?>