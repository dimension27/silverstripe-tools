<?php
/**
 * Composite field that represents a measure composed of value and units.
 * @package SSTools_Forms
 */
class SSTools_Forms_MeasureField extends CompositeField {
	
	/**
	 * @var $columnCount int Toggle different css-rendering for multiple columns 
	 * ("onecolumn", "twocolumns", "threecolumns"). The content is determined
	 * by the $children-array, so wrap all items you want to have grouped in a
	 * column inside a CompositeField.
	 * Caution: Please make sure that this variable actually matches the 
	 * count of your $children.
	 */
	protected $columnCount = 2;
	
	public function __construct($valueField, $unitsField = null) {
		if (is_string($valueField)) {
			$valueField = new NumericField($valueField);
		}
		if (is_null($unitsField)) {
			$unitsField = $valueField->Name().'Units';
		}
		if (is_string($unitsField)) {
			$unitsField = new DropDownField($unitsField);
		}
		parent::__construct(array($valueField, $unitsField));
		$this->addExtraClass('MeasureField');
	}
	
	/**
	 * Returns the fields nested inside another DIV
	 */
	function FieldHolder() {
		return $this->getFieldHolderContent('Field');
	}
	
	/**
	 * Returns the fields in the restricted field holder inside a DIV.
	 */
	function SmallFieldHolder() {
		return $this->getFieldHolderContent('Field');
	}	
}

?>