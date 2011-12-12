<?php

class FamilyGathering {

	static function getDescendants( $dataObject, $className, $sort ) {
		$parentIDs = $dataObject->getDescendantIDList();
		$parentIDs[] = $this->data()->ID;
		return DataObject::get($className, "ParentID IN ('".implode($parentIDs, "', '"). "')", $sort);
	}

}

?>