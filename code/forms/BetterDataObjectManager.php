<?php

class BetterDataObjectManager extends DataObjectManager {

	function setFieldList( $fieldList ) {
		$this->fieldList = $fieldList;
	}

	function addField( $fieldName, $fieldTitle ) {
		$this->fieldList[$fieldName] = $fieldTitle;
	}

	function prependField( $fieldName, $fieldTitle ) {
		$this->fieldList = array_merge(array($fieldName => $fieldTitle), $this->fieldList);
	}

	function removeField( $fieldName ) {
		unset($this->fieldList[$fieldName]);
	}

	function removeFields( $args ) {
		if( !is_array($args) ) {
			$args = func_get_args();
		}
		foreach( $args as $field ) {
			$this->removeField($field);
		}
	}

}