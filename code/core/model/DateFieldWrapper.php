<?php
/**
 * Wraps around a DataObject to provide the ability to set and get date/datetime fields using DateTime instances.
 * Delegates all other calls (save write) to the wrapped object.
 * NB: DateTime instances are not converted until write() is called, 
 * and if the existing value in the wrapped object is modified in the meantime through another method, 
 * it will be overwritten.
 * NB: does not wrap the built-in Created and LastEdited fields.
 * @author sergeim
 */
class SSTools_Core_Model_DateFieldWrapper extends Object {
	
	/**
	 * The names of the date/datetime fields wrapped by this object.
	 * @var array
	 */
	protected $wrappedFields = array();
	
	/**
	 * The current values for the wrapped fields.
	 * @var array
	 */
	protected $wrappedFieldValues = array();
	
	/**
	 * The wrapped data object instance.
	 * @var DataObject
	 */
	protected $wrapped = null;
	
	public function __construct(DataObject $wrapped) {
		foreach ($wrapped->db() as $fieldName => $type) {
			switch (strtolower($type)) {
				case 'date':
				case 'datetime':
					$this->wrappedFields[] = $fieldName;
					break;
			}
		}
		$this->wrapped = $wrapped;
	}
	
	public function __call($method, $arguments) {
		return call_user_func_array(array($this->wrapped, $method), $arguments);
	}
	
	public function __get($property) {
		if ($this->isWrappedField($property)) {
			$rv = null;
			if (isset($this->wrappedFieldValues[$property])) {
				$rv = $this->wrappedFieldValues[$property];
			}
			else {
				if ($value = $this->wrapped->$property) {
					$rv = $this->wrappedFieldValues[$property] = new DateTime($value);;
				}
			}
			return $rv;
		}
		return $this->wrapped->$property;
	}
	
	public function __set($property, $value) {
		if ($this->isWrappedField($property)) {
			$this->wrappedFieldValues[$property] = $value;
		}
		else {
			$this->wrapped->$property = $value;
		}
	}
	
	/**
	 * Returns the wrapped fields.
	 */
	public function getWrappedFields() {
		return $this->wrappedFields;
	}
	
	/**
	 * Returns true if the specified $property is a wrapped field.
	 * @param string $property
	 */
	public function isWrappedField($property) {
		return in_array($property, $this->wrappedFields);
	}
	
	/**
	 * Writes all changes to the wrapped object to the database.
	 * @see DataObject::write()
	 * @return int The ID of the record
	 * @throws ValidationException Exception that can be caught and handled by the calling function
	 */
	public function write($showDebug = false, $forceInsert = false, $forceWrite = false, $writeComponents = false) {
		foreach ($this->wrappedFieldValues as $property => $value) {
			if (is_object($value)) {
				$value = $value->format('c');
			}
			$this->wrapped->$property = $value;
		}
		return $this->wrapped->write($showDebug , $forceInsert , $forceWrite , $writeComponents );
	}
}