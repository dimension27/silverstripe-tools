<?php
class OptimisedManyManyDataObjectManager extends ManyManyDataObjectManager {

	protected $dataObject = false;
	public $templatePopup = "OptimisedManyMany_popup";
	
	/**
	 * The ManyManyDataObjectManager is horribly inefficent and doesn't scale well
	 * This class modifies the standard behaviour to only show attached DataObjects but allow simple searching on unattached DataObjects
	 *
	 * @authur Adam Rice <development@hashnotadam.com>
	 */
	function __construct($controller, $name, $sourceClass, $fieldList = null, $detailFormFields = null, $sourceFilter = "", $sourceSort = "", $sourceJoin = "") {
		parent::__construct($controller, $name, $sourceClass, $fieldList, $detailFormFields, $sourceFilter, $sourceSort, $sourceJoin);

		$classes = array_reverse(ClassInfo::ancestry($this->controllerClass()));
		foreach($classes as $class) {
			if($class != "Object") {
				$singleton = singleton($class);
				$manyManyRelations = $singleton->uninherited('many_many', true);
				if(isset($manyManyRelations) && array_key_exists($this->name, $manyManyRelations)) {
					$this->manyManyParentClass = $class;
					$manyManyTable = $class . '_' . $this->name;
					break;
				}

				$belongsManyManyRelations = $singleton->uninherited( 'belongs_many_many', true );
				 if( isset( $belongsManyManyRelations ) && array_key_exists( $this->name, $belongsManyManyRelations ) ) {
					$this->manyManyParentClass = $class;
					
					// @modification http://open.silverstripe.org/ticket/5194
					$manyManyClass = $belongsManyManyRelations[$this->name];
					$manyManyRelations = singleton($manyManyClass)->uninherited('many_many', true);
					foreach($manyManyRelations as $manyManyRelationship => $manyManyChildClass)
						if ($manyManyChildClass == $class)
							break;
					
					$manyManyTable = $manyManyClass . '_' . $manyManyRelationship;
					break;
				}
			}
		}

		$tableClasses = ClassInfo::dataClassesFor($this->sourceClass);
		$this->source = array_shift($tableClasses);
		$sourceField = ($this->manyManyParentClass == $this->sourceClass ? 'Child' : $this->sourceClass);
		$condition = " JOIN \"$this->manyManyTable\" ON (\"{$this->source}\".\"ID\" = \"{$sourceField}ID\" AND \"$this->manyManyTable\".\"{$this->manyManyParentClass}ID\" = '{$this->controller->ID}')";
		$this->sourceJoin = str_replace('LEFT' . $condition, 'INNER' . $condition, $this->sourceJoin);
		$this->searchJoin = "LEFT JOIN {$this->manyManyTable} ON ({$this->manyManyTable}.{$sourceField}ID = {$this->source}.ID AND {$this->manyManyTable}.{$this->manyManyParentClass}ID = {$this->controller->ID})";
		$this->searchCondition = "isNull({$this->manyManyTable}.ID)";
	}
	
	function attach() {
		if( $id = $_GET['id'] )
			$saveDest = $this->controller->{$this->name}()->add($id);
		return $id;
	}
	
	function getDataObject() {
		if( !$this->dataObject ) $this->setDataObject();
		return $this->dataObject;
	}
	
	function setDataObject() {
		$this->dataObject = new $this->sourceClass;
	}
	
	function search() {
		$returnSet = array(
			'results' => array(),
			'xhrIndex' => isset($_GET['xhrIndex']) ? (int)$_GET['xhrIndex'] : 0
		);

		if( (isset($_GET[$this->class])) && ($fields = $_GET[$this->class]) ) {
			$dataObject = $this->getDataObject();
			$table = $dataObject->baseTable();
			$searchableFields = array_keys($dataObject->SearchableFields(true));
			$select = "SELECT {$this->source}.ID, {$this->source}." . implode(", {$this->source}.", $searchableFields) . ", {$this->manyManyTable}.{$this->manyManyParentClass}ID AS RelationshipID FROM {$table} {$this->searchJoin}";

			if( $fields ) {
				$select .= ' WHERE ';
				$where = array();
				foreach( $fields as $key => $value )
					$where[] = "{$table}.{$key} LIKE '{$value}%'";
				$select .= implode(' AND ', $where);
			}
		
			$sql = $select . ' LIMIT ' . (isset($_GET['limit']) ? $_GET['limit'] : 10);
			$results = DB::query($sql);
		
			while( $result = $results->next() )
				$returnSet['results'][] = $result;
		}

		return json_encode($returnSet);
	}
	
	function SearchableFields( $includeID = false ) {
		if( !isset($this->searchableFields) ) $this->searchableFieldsSetter();
		$fields = clone $this->searchableFields;
		if( !$includeID ) $fields->shift();
		return $fields;
	}
	
	function searchableFieldsSetter() {
		$dataObject = $this->getDataObject();
		$fields = array();
		$labels = $dataObject->fieldLabels();
		$searchableFields = $dataObject->searchableFields();
		$this->searchableFields = new DataObjectSet;
		
		$field = new DataObject;
		$field->Link = $this->class . '[ID]';
		$field->Name = 'ID';
		$field->URLSegment = 'ID';
		$this->searchableFields->push($field);
		
		foreach( $searchableFields as $field )
			$fields[] = $field['title'];
		foreach( $labels as $key => $value ) {
			if( in_array($value, $fields) ) {
				$field = new DataObject;
				$field->ID = $this->class . "[$key]";
				$field->Name = $value;
				$field->HTMLClass = $key;
				$this->searchableFields->push($field);
			}
		}
		
		$this->TableColumnWidth = (90 / ($this->searchableFields->TotalItems() - 1)) . '%';
	}
	
	function SearchableResultsTableBody() {
		$fields = $this->SearchableFields();
		$numRows = 7 - ceil($fields->TotalItems() / 5);
		$row = $rows = '';		
		
		foreach( $fields as $field )
			$row .= "<td class=\"{$field->HTMLClass}\">&nbsp;</td>";
		$row .= '<td class="Add last">&nbsp;</td>';
		
		for( $i = 0; $i < $numRows; $i++ )
			$rows .= '<tr class="' . ($i%2 ? 'even' : 'odd') . '" data-row-id="0">' . $row . '</tr>';
		
		return $rows;
	}
	
	function getTitleFieldName() {
		$dataObject = $this->getDataObject();
		foreach( array('Title', 'Name') as $value ) {
			if($dataObject->hasDatabaseField($value)) return $dataObject->getField($value);
		}
		return 'ID';
	}
	
	/**
	 * Whether or not to include search in the popup. This should not be done if we are editing an existing record.
	 */
	function IncludeSearch() {
		return !$this->getDataObject()->exists();
	}
}